<?php
namespace App\Http\Controllers\Backend\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Section\StoreSectionRequest;
use App\Http\Requests\Section\UpdateSectionRequest;
use App\Models\Column\Section;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    /**
     * 区块列表
     */
    public function index(Request $request): JsonResponse
    {
        $model = $request->get('model');
        $query = Section::with(['column:id,name'])->orderBy('sort');

        if ($request->has('column_id') && !empty($request->get('column_id'))) {
            $query->where('column_id', $request->get('column_id'));
        }

        if ($model === 'hasdel') {
            $res = $query->onlyTrashed()->paginate(
                $request->get('pageSize', $request->get('limit', 10))
            )->toArray();
        } else {
            $res = $query->paginate(
                $request->get('pageSize', $request->get('limit', 10))
            )->toArray();
        }

        return response()->json([
            'status' => true,
            'data' => $res['data'],
            'total' => $res['total'],
        ]);
    }

    /**
     * 所有区块（用于下拉选择）
     */
    public function data(Request $request): JsonResponse
    {
        $query = Section::active();

        if ($request->has('column_id')) {
            $query->where('column_id', $request->column_id);
        }

        $sections = $query->orderBy('sort')->get();

        return response()->json([
            'status' => true,
            'data' => $sections,
        ]);
    }

    /**
     * 创建区块
     */
    public function store(StoreSectionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (Section::create($validated)) {
            return response()->json(['status' => true, 'msg' => '成功添加区块']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 区块详情
     */
    public function show(Section $section): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $section->load('column'),
        ]);
    }

    /**
     * 更新区块
     */
    public function update(UpdateSectionRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $section = Section::findOrFail($id);
        if ($section->update($validated)) {
            return response()->json(['status' => true, 'msg' => '成功更新区块']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除区块（软删除）
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Section::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '成功删除区块']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 恢复区块
     */
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $section = Section::withTrashed()->find($id);
        if ($section->restore()) {
            return response()->json(['status' => true, 'msg' => '成功恢复区块']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 强制删除区块
     */
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $section = Section::withTrashed()->find($id);
        if ($section->forceDelete()) {
            return response()->json(['status' => true, 'msg' => '成功强制删除区块']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
}