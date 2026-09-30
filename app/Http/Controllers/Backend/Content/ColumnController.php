<?php
namespace App\Http\Controllers\Backend\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Column\StoreColumnRequest;
use App\Http\Requests\Column\UpdateColumnRequest;
use App\Models\Column\Column;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ColumnController extends Controller
{
    /**
     * 栏目列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Column::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }

        if (!empty($request->get('model')) && $request->get('model') === 'hasdel') {
            $query = $query->onlyTrashed();
        }

        $res = $query->orderBy('sort')->paginate(
            $request->get('pageSize', $request->get('limit', 10))
        )->toArray();

        foreach ($res['data'] as &$row) {
            $row['banner_ids'] = \DB::table('content_banner_column')
                ->where('column_id', $row['id'])
                ->pluck('banner_id')
                ->all();
        }

        $result = [
            'status' => true,
            'data' => $res['data'],
            'total' => $res['total'],
        ];

        return response()->json($result);
    }

    /**
     * 创建栏目
     */
    public function store(StoreColumnRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $column = Column::create($validated);

        if (array_key_exists('banner_ids', $validated)) {
            $column->banners()->sync($validated['banner_ids'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => '栏目创建成功',
            'data' => $column,
        ], 201);
    }

    /**
     * 栏目详情
     */
    public function show(Column $column): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $column->load('nav'),
        ]);
    }

    /**
     * 更新栏目
     */
    public function update(UpdateColumnRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $column = Column::findOrFail($id);

        $column->update($validated);

        if (array_key_exists('banner_ids', $validated)) {
            $column->banners()->sync($validated['banner_ids'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => '栏目更新成功',
            'data' => $column,
        ]);
    }

    /**
     * 所有栏目（用于下拉选择）
     */
    public function data(): JsonResponse
    {
        $columns = Column::select('id', 'name', 'pId')->orderBy('sort')->get();

        return response()->json([
            'status' => true,
            'data' => $columns,
        ]);
    }

    /**
     * 删除栏目
     */
    public function destroy(Column $column): JsonResponse
    {
        $column->delete();

        return response()->json([
            'success' => true,
            'message' => '栏目删除成功',
        ]);
    }
}

