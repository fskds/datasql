<?php

namespace App\Http\Controllers\Backend\Acupoint;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acupoint\StoreMeridianRequest;
use App\Http\Requests\Acupoint\UpdateMeridianRequest;
use App\Models\Acupoint\Meridian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeridianController extends Controller
{
    /**
     * 经脉列表（分页，支持回收站 model=hasdel）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Meridian::select(
                'id', 'code', 'cn', 'pinyin', 'type', 'color', 'desc', 'sort', 'status'
            );

            if ($request->filled('code')) {
                $query->where('code', 'like', '%' . $request->get('code') . '%');
            }
            if ($request->filled('cn')) {
                $query->where('cn', 'like', '%' . $request->get('cn') . '%');
            }
            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }
            if ($request->get('model') === 'hasdel') {
                $query->onlyTrashed();
            }

            $current = max(1, (int) $request->get('current', 1));
            $pageSize = max(1, (int) $request->get('pageSize', 10));
            $res = $query->orderBy('sort', 'asc')->orderBy('id', 'asc')
                ->paginate($pageSize, ['*'], 'page', $current);

            return response()->json([
                'status' => true,
                'data' => $res->items(),
                'total' => $res->total(),
                'pageSize' => $res->perPage(),
                'current' => $res->currentPage(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'total' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 全部可用经脉（下拉选项）
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $list = Meridian::where('status', 1)
                ->orderBy('sort', 'asc')
                ->get(['id', 'code', 'cn', 'type', 'color']);

            $data = $list->map(function ($item) {
                return [
                    'label' => $item->cn . '（' . $item->code . '）',
                    'value' => $item->id,
                    'code' => $item->code,
                    'type' => $item->type,
                    'color' => $item->color,
                ];
            });

            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 经脉详情
     */
    public function show(Request $request, $id): JsonResponse
    {
        $meridian = Meridian::findOrFail($id);
        return response()->json(['status' => true, 'data' => $meridian]);
    }

    /**
     * 创建经脉
     */
    public function store(StoreMeridianRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = (int) ($data['status'] ?? 1);
        $data['sort'] = (int) ($data['sort'] ?? 0);

        $meridian = Meridian::create($data);

        return $meridian
            ? response()->json(['status' => true, 'msg' => '创建经脉成功'])
            : response()->json(['status' => false, 'msg' => '创建失败，请联系管理员'], 500);
    }

    /**
     * 更新经脉
     */
    public function update(UpdateMeridianRequest $request, $id): JsonResponse
    {
        $meridian = Meridian::findOrFail($id);
        $data = $request->validated();

        $updated = $meridian->update($data);

        return $updated
            ? response()->json(['status' => true, 'msg' => '更新经脉成功'])
            : response()->json(['status' => false, 'msg' => '更新失败，请联系管理员'], 500);
    }

    /**
     * 删除（软删除）
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = (array) $request->get('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        Meridian::whereIn('id', $ids)->delete();

        return response()->json(['status' => true, 'msg' => '删除成功']);
    }

    /**
     * 恢复
     */
    public function restore(Request $request): JsonResponse
    {
        $ids = (array) $request->get('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择恢复项']);
        }

        Meridian::withTrashed()->whereIn('id', $ids)->restore();

        return response()->json(['status' => true, 'msg' => '恢复成功']);
    }

    /**
     * 彻底删除
     */
    public function force(Request $request): JsonResponse
    {
        $ids = (array) $request->get('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }

        Meridian::withTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['status' => true, 'msg' => '彻底删除成功']);
    }
}
