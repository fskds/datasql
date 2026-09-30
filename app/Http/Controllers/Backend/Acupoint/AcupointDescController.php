<?php

namespace App\Http\Controllers\Backend\Acupoint;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acupoint\StoreAcupointDescRequest;
use App\Http\Requests\Acupoint\UpdateAcupointDescRequest;
use App\Models\Acupoint\AcupointDesc;
use App\Models\Acupoint\Meridian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcupointDescController extends Controller
{
    /**
     * 穴位介绍列表（分页，支持回收站 model=hasdel）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = AcupointDesc::select(
                'id', 'meridian_code', 'name', 'description', 'acupoint_id',
                'sort', 'status'
            );

            if ($request->filled('meridian_code')) {
                $query->where('meridian_code', $request->get('meridian_code'));
            }
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            }
            if ($request->filled('acupoint_id')) {
                $query->where('acupoint_id', (int) $request->get('acupoint_id'));
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
                    'value' => $item->code,
                ];
            });
            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 穴位介绍详情
     */
    public function show(Request $request, $id): JsonResponse
    {
        $desc = AcupointDesc::findOrFail($id);
        return response()->json(['status' => true, 'data' => $desc]);
    }

    /**
     * 创建穴位介绍
     */
    public function store(StoreAcupointDescRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = (int) ($data['status'] ?? 1);
        $data['sort'] = (int) ($data['sort'] ?? 0);

        $desc = AcupointDesc::create($data);

        return $desc
            ? response()->json(['status' => true, 'msg' => '创建穴位介绍成功'])
            : response()->json(['status' => false, 'msg' => '创建失败，请联系管理员'], 500);
    }

    /**
     * 更新穴位介绍
     */
    public function update(UpdateAcupointDescRequest $request, $id): JsonResponse
    {
        $desc = AcupointDesc::findOrFail($id);
        $data = $request->validated();

        $updated = $desc->update($data);

        return $updated
            ? response()->json(['status' => true, 'msg' => '更新穴位介绍成功'])
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

        AcupointDesc::whereIn('id', $ids)->delete();

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

        AcupointDesc::withTrashed()->whereIn('id', $ids)->restore();

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

        AcupointDesc::withTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['status' => true, 'msg' => '彻底删除成功']);
    }
}
