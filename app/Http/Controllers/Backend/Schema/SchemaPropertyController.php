<?php

namespace App\Http\Controllers\Backend\Schema;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schema\StoreSchemaPropertyRequest;
use App\Http\Requests\Schema\UpdateSchemaPropertyRequest;
use App\Models\Schema\SchemaProperty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchemaPropertyController extends Controller
{
    /**
     * 属性列表（分页，支持回收站 model=hasdel）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SchemaProperty::select(
                'id', 'type_id', 'name', 'slug', 'expected_type',
                'description', 'url', 'sort', 'status'
            )->with('schemaType:id,name');

            if ($request->filled('type_id')) {
                $query->where('type_id', (int) $request->get('type_id'));
            }

            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            }

            if ($request->filled('expected_type')) {
                $query->where('expected_type', 'like', '%' . $request->get('expected_type') . '%');
            }

            if ($request->get('model') === 'hasdel') {
                $query->onlyTrashed();
            }

            $current = max(1, (int) $request->get('current', 1));
            $pageSize = max(1, (int) $request->get('pageSize', 10));
            $res = $query->orderBy('sort', 'asc')->orderBy('id', 'asc')
                ->paginate($pageSize, ['*'], 'page', $current);

            $data = array_map(function ($item) {
                $item->type_name = $item->schemaType ? $item->schemaType->name : '';
                unset($item->schemaType);
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
     * 全部可用类型（用于属性编辑时下拉选择）
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $types = \App\Models\Schema\SchemaType::where('status', 1)
                ->orderBy('sort', 'asc')->get(['id', 'parent_id', 'name']);
            $tree = $this->buildOptions($types, 0);

            return response()->json(['status' => true, 'data' => $tree]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 属性详情
     */
    public function show(Request $request, $id): JsonResponse
    {
        $property = SchemaProperty::with('schemaType:id,name')->findOrFail($id);
        $property->type_name = $property->schemaType ? $property->schemaType->name : '';
        unset($property->schemaType);

        return response()->json(['status' => true, 'data' => $property]);
    }

    /**
     * 创建属性
     */
    public function store(StoreSchemaPropertyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['type_id'] = (int) $data['type_id'];
        $data['status'] = (int) ($data['status'] ?? 1);

        $property = SchemaProperty::create($data);

        return $property
            ? response()->json(['status' => true, 'msg' => '创建属性成功'])
            : response()->json(['status' => false, 'msg' => '创建失败，请联系管理员'], 500);
    }

    /**
     * 更新属性
     */
    public function update(UpdateSchemaPropertyRequest $request, $id): JsonResponse
    {
        $property = SchemaProperty::findOrFail($id);
        $data = $request->validated();

        if (array_key_exists('type_id', $data)) {
            $data['type_id'] = (int) $data['type_id'];
        }

        $updated = $property->update($data);

        return $updated
            ? response()->json(['status' => true, 'msg' => '更新属性成功'])
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

        SchemaProperty::whereIn('id', $ids)->delete();

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

        SchemaProperty::withTrashed()->whereIn('id', $ids)->restore();

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

        SchemaProperty::withTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['status' => true, 'msg' => '彻底删除成功']);
    }

    //---------------- 私有辅助方法 ----------------

    private function buildOptions($types, int $parentId): array
    {
        $options = [];
        foreach ($types as $type) {
            if ((int) $type->parent_id === $parentId) {
                $node = [
                    'title' => $type->name,
                    'value' => $type->id,
                ];
                $children = $this->buildOptions($types, (int) $type->id);
                if (!empty($children)) {
                    $node['children'] = $children;
                }
                $options[] = $node;
            }
        }
        return $options;
    }
}