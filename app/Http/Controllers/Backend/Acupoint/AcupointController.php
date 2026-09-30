<?php

namespace App\Http\Controllers\Backend\Acupoint;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acupoint\StoreAcupointRequest;
use App\Http\Requests\Acupoint\UpdateAcupointRequest;
use App\Models\Acupoint\Acupoint;
use App\Models\Acupoint\Meridian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcupointController extends Controller
{
    /**
     * 穴道列表（分页，支持回收站 model=hasdel）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Acupoint::select(
                'id', 'point_id', 'meridian_id', 'meridian_code', 'side', 'seq',
                'name', 'meridian_cn', 'color', 'obj',
                'scale_x', 'scale_y', 'scale_z',
                'x', 'y', 'z', 'sort', 'status'
            )->with('meridian:id,code,cn');

            if ($request->filled('meridian_id')) {
                $query->where('meridian_id', (int) $request->get('meridian_id'));
            }
            if ($request->filled('meridian_code')) {
                $query->where('meridian_code', $request->get('meridian_code'));
            }
            if ($request->filled('side')) {
                $query->where('side', $request->get('side'));
            }
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            }
            if ($request->filled('point_id')) {
                $query->where('point_id', 'like', '%' . $request->get('point_id') . '%');
            }
            if ($request->get('model') === 'hasdel') {
                $query->onlyTrashed();
            }

            $current = max(1, (int) $request->get('current', 1));
            $pageSize = max(1, (int) $request->get('pageSize', 10));
            $res = $query->orderBy('sort', 'asc')->orderBy('id', 'asc')
                ->paginate($pageSize, ['*'], 'page', $current);

            $data = array_map(function ($item) {
                $item->meridian_name = $item->meridian ? $item->meridian->code : '';
                unset($item->meridian);
                return $item;
            }, $res->items());

            return response()->json([
                'status' => true,
                'data' => $data,
                'total' => $res->total(),
                'pageSize' => $res->perPage(),
                'current' => $res->currentPage(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'total' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 全部经脉（下拉选项）
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $list = Meridian::where('status', 1)
                ->orderBy('sort', 'asc')
                ->get(['id', 'code', 'cn']);
            $data = $list->map(function ($item) {
                return [
                    'label' => $item->cn . '（' . $item->code . '）',
                    'value' => $item->id,
                    'code' => $item->code,
                ];
            });
            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 穴道详情
     */
    public function show(Request $request, $id): JsonResponse
    {
        $acupoint = Acupoint::with('meridian:id,code,cn')->findOrFail($id);
        return response()->json(['status' => true, 'data' => $acupoint]);
    }

    /**
     * 创建穴道
     */
    public function store(StoreAcupointRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = (int) ($data['status'] ?? 1);
        $data['sort'] = (int) ($data['sort'] ?? 0);
        // 冗余 meridian_code / meridian_cn
        if (!empty($data['meridian_id'])) {
            $m = Meridian::find($data['meridian_id']);
            if ($m) {
                $data['meridian_code'] = $data['meridian_code'] ?? $m->code;
                $data['meridian_cn'] = $data['meridian_cn'] ?? $m->cn;
                $data['color'] = $data['color'] ?? $m->color;
            }
        }

        $acupoint = Acupoint::create($data);

        return $acupoint
            ? response()->json(['status' => true, 'msg' => '创建穴道成功'])
            : response()->json(['status' => false, 'msg' => '创建失败，请联系管理员'], 500);
    }

    /**
     * 更新穴道
     */
    public function update(UpdateAcupointRequest $request, $id): JsonResponse
    {
        $acupoint = Acupoint::findOrFail($id);
        $data = $request->validated();
        // 同步冗余字段
        if (!empty($data['meridian_id'])) {
            $m = Meridian::find($data['meridian_id']);
            if ($m) {
                $data['meridian_code'] = $data['meridian_code'] ?? $m->code;
                $data['meridian_cn'] = $data['meridian_cn'] ?? $m->cn;
                $data['color'] = $data['color'] ?? $m->color;
            }
        }

        $updated = $acupoint->update($data);

        return $updated
            ? response()->json(['status' => true, 'msg' => '更新穴道成功'])
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

        Acupoint::whereIn('id', $ids)->delete();

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

        Acupoint::withTrashed()->whereIn('id', $ids)->restore();

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

        Acupoint::withTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['status' => true, 'msg' => '彻底删除成功']);
    }
}
