<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Acupoint\AcupointDesc;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AcupointController extends Controller
{
    /**
     * 站点完整数据：经脉 + 经络线 + 穴道 + 模型装配数据。
     *
     * 数据策略：全部从数据库读取，不再依赖 renti 的 site-data.json。
     *   - meridians   来自 meridians 表
     *   - lines       来自 meridian_lines 表（pos 由 pos_x/pos_y/pos_z 拼接）
     *   - acupoints   来自 acupoints 表（id 用原始 point_id，scale 拼接）
     *   - model.skin / model.bones 来自 model_parts 表
     *
     * 返回结构严格对齐 renti/src/api/types.ts 的 SiteData。
     */
    public function siteData(): JsonResponse
    {
        try {
            $meridians = DB::table('meridians')
                ->where('status', 1)
                ->orderBy('sort')->orderBy('id')
                ->get(['code', 'cn', 'pinyin', 'type', 'color', 'desc'])
                ->map(function ($m) {
                    return [
                        'code' => $m->code,
                        'cn' => $m->cn,
                        'pinyin' => $m->pinyin,
                        'type' => $m->type,
                        'color' => $m->color,
                        'desc' => $m->desc,
                    ];
                })->values()->all();

            $lines = DB::table('meridian_lines')
                ->where('status', 1)
                ->orderBy('sort')->orderBy('id')
                ->get(['meridian_code', 'side', 'color', 'obj', 'pos_x', 'pos_y', 'pos_z'])
                ->map(function ($l) {
                    return [
                        'meridian' => $l->meridian_code,
                        'side'     => $l->side,
                        'color'    => $l->color,
                        'obj'      => $l->obj,
                        'pos'      => [(float) $l->pos_x, (float) $l->pos_y, (float) $l->pos_z],
                    ];
                })->values()->all();

            $acupoints = DB::table('acupoints')
                ->where('status', 1)
                ->orderBy('sort')->orderBy('id')
                ->get(['point_id', 'meridian_code', 'side', 'seq', 'name', 'meridian_cn', 'color', 'obj', 'scale_x', 'scale_y', 'scale_z', 'x', 'y', 'z'])
                ->map(function ($a) {
                    return [
                        'id'        => $a->point_id,
                        'meridian'  => $a->meridian_code,
                        'side'      => $a->side,
                        'seq'       => (int) $a->seq,
                        'name'      => $a->name,
                        'meridianCn'=> $a->meridian_cn,
                        'color'     => $a->color,
                        'obj'       => $a->obj,
                        'scale'     => [(float) $a->scale_x, (float) $a->scale_y, (float) $a->scale_z],
                        'x'         => (float) $a->x,
                        'y'         => (float) $a->y,
                        'z'         => (float) $a->z,
                    ];
                })->values()->all();

            $skin = [];
            $bones = [];
            $parts = DB::table('model_parts')
                ->where('status', 1)
                ->orderBy('sort')->orderBy('id')
                ->get(['part_type', 'name', 'obj', 'layer', 'at_json']);
            foreach ($parts as $p) {
                $item = [
                    'name'  => $p->name,
                    'obj'   => $p->obj,
                    'layer' => $p->layer,
                    'at'    => json_decode($p->at_json, true) ?? [],
                ];
                if ($p->part_type === 'bones') {
                    $bones[] = $item;
                } else {
                    $skin[] = $item;
                }
            }
        } catch (\Throwable $e) {
            $meridians = [];
            $lines = [];
            $acupoints = [];
            $skin = [];
            $bones = [];
        }

        return response()->json([
            'meridians' => $meridians,
            'lines'     => $lines,
            'acupoints' => $acupoints,
            'model'     => ['skin' => $skin, 'bones' => $bones],
        ])->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public, max-age=60',
        ]);
    }

    /**
     * 穴道介绍：返回 { `${meridian_code}:${name}`: description } map。
     * 前端按 lineKey 查找介绍文字。
     *
     * 来自数据库 acupoint_descs 表，后台可编辑。
     * 若数据库为空，fallback 到 renti/public/data/acupoint_descs.json。
     */
    public function acupointDescs(): JsonResponse
    {
        $result = [];

        // 1. 优先从数据库读取（后台可编辑）
        try {
            $rows = AcupointDesc::orderBy('sort')->orderBy('id')->get();
            foreach ($rows as $d) {
                $result["{$d->meridian_code}:{$d->name}"] = $d->description;
                // 同时支持仅按 name 查找（兼容前端两种查找方式）
                $result[$d->name] = $d->description;
            }
        } catch (\Throwable $e) {
            // 数据库异常时 fallback 到 JSON 文件
        }

        // 2. 若数据库无数据，从 renti 原始 JSON 文件加载
        // if (empty($result)) {
            // $path = base_path('../renti/data-source/acupoint_descs.json');
            // if (File::exists($path)) {
                // try {
                    // $json = json_decode(File::get($path), true);
                    // if (is_array($json)) {
                        // $result = $json;
                    // }
                // } catch (\Throwable $e) {
                    //忽略
                // }
            // }
        // }

        return response()->json($result)->withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public, max-age=60',
        ]);
    }
}
