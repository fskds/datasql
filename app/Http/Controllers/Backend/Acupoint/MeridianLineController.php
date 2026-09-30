<?php

namespace App\Http\Controllers\Backend\Acupoint;

use App\Http\Controllers\Controller;
use App\Http\Requests\Acupoint\StoreMeridianLineRequest;
use App\Http\Requests\Acupoint\UpdateMeridianLineRequest;
use App\Models\Acupoint\Meridian;
use App\Models\Acupoint\MeridianLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeridianLineController extends Controller
{
    /**
     * 经脉线列表（分页，支持回收站 model=hasdel）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MeridianLine::select(
                'id', 'meridian_id', 'meridian_code', 'side', 'color', 'obj',
                'pos_x', 'pos_y', 'pos_z', 'sort', 'status'
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
            if ($request->get('model') === 'hasdel') {
                $query->onlyTrashed();
            }

            $current = max(1, (int) $request->get('current', 1));
            $pageSize = max(1, (int) $request->get('pageSize', 10));
            $res = $query->orderBy('sort', 'asc')->orderBy('id', 'asc')
                ->paginate($pageSize, ['*'], 'page', $current);

            $data = array_map(function ($item) {
                $item->meridian_cn = $item->meridian ? $item->meridian->cn : '';
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
     * 经脉线详情
     */
    public function show(Request $request, $id): JsonResponse
    {
        $line = MeridianLine::with('meridian:id,code,cn')->findOrFail($id);
        return response()->json(['status' => true, 'data' => $line]);
    }

    /**
     * 创建经脉线
     */
    public function store(StoreMeridianLineRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = (int) ($data['status'] ?? 1);
        $data['sort'] = (int) ($data['sort'] ?? 0);
        // 冗余 meridian_code
        if (empty($data['meridian_code']) && !empty($data['meridian_id'])) {
            $m = Meridian::find($data['meridian_id']);
            $data['meridian_code'] = $m ? $m->code : '';
        }

        $line = MeridianLine::create($data);

        return $line
            ? response()->json(['status' => true, 'msg' => '创建经脉线成功'])
            : response()->json(['status' => false, 'msg' => '创建失败，请联系管理员'], 500);
    }

    /**
     * 更新经脉线
     */
    public function update(UpdateMeridianLineRequest $request, $id): JsonResponse
    {
        $line = MeridianLine::findOrFail($id);
        $data = $request->validated();
        // 同步冗余 meridian_code
        if (!empty($data['meridian_id']) && empty($data['meridian_code'])) {
            $m = Meridian::find($data['meridian_id']);
            $data['meridian_code'] = $m ? $m->code : $line->meridian_code;
        }

        $updated = $line->update($data);

        return $updated
            ? response()->json(['status' => true, 'msg' => '更新经脉线成功'])
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

        MeridianLine::whereIn('id', $ids)->delete();

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

        MeridianLine::withTrashed()->whereIn('id', $ids)->restore();

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

        MeridianLine::withTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['status' => true, 'msg' => '彻底删除成功']);
    }
}
