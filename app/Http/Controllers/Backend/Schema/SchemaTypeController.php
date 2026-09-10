<?php

namespace App\Http\Controllers\Backend\Schema;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schema\StoreSchemaTypeRequest;
use App\Http\Requests\Schema\UpdateSchemaTypeRequest;
use App\Models\Schema\SchemaType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchemaTypeController extends Controller
{
    /**
     * 类型列表（分页，支持回收站 model=hasdel）
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SchemaType::select('id', 'parent_id', 'name', 'slug', 'is_leaf', 'depth', 'description', 'example', 'url', 'sort', 'status')->with('parent:id,name');

            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->get('name') . '%');
            }

            if ($request->get('model') === 'hasdel') {
                $query->onlyTrashed();
            }

            $current = max(1, (int) $request->get('current', 1));
            $pageSize = max(1, (int) $request->get('pageSize', 10));
            $res = $query->orderBy('sort', 'asc')->paginate($pageSize, ['*'], 'page', $current);

            $data = array_map(function ($item) {
                return $this->appendParentName($item);
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
     * 全部类型，用于下拉/树选择（仅未删除）
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $types = SchemaType::where('status', 1)->orderBy('sort', 'asc')->get(['id', 'parent_id', 'name']);
            $tree = $this->buildOptions($types, 0);

            return response()->json(['status' => true, 'data' => $tree]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'data' => [], 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 类型详情
     */
    public function show(Request $request, $id): JsonResponse
    {
        $type = SchemaType::with('parent:id,name')->findOrFail($id);

        return response()->json(['status' => true, 'data' => $this->appendParentName($type)]);
    }

    /**
     * 创建类型
     */
    public function store(StoreSchemaTypeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['parent_id'] = (int) ($data['parent_id'] ?? 0);
        $data['is_leaf'] = (int) ($data['is_leaf'] ?? 1);
        $data['depth'] = $this->resolveDepth((int) $data['parent_id']);
        $data['status'] = (int) ($data['status'] ?? 1);

        $type = SchemaType::create($data);

        if ($data['parent_id'] > 0) {
            SchemaType::where('id', $data['parent_id'])->where('is_leaf', 1)->update(['is_leaf' => 0]);
        }

        return $type
            ? response()->json(['status' => true, 'msg' => '创建类型成功'])
            : response()->json(['status' => false, 'msg' => '创建失败，请联系管理员'], 500);
    }

    /**
     * 更新类型
     */
    public function update(UpdateSchemaTypeRequest $request, $id): JsonResponse
    {
        $type = SchemaType::findOrFail($id);
        $data = $request->validated();

        if (array_key_exists('parent_id', $data) && (int) $data['parent_id'] === (int) $id) {
            return response()->json(['status' => false, 'msg' => '父类型不能是自身']);
        }

        if (array_key_exists('parent_id', $data)) {
            $data['parent_id'] = (int) $data['parent_id'];
            $data['depth'] = $this->resolveDepth($data['parent_id']);
        }

        $updated = $type->update($data);

        if ($updated && isset($data['parent_id']) && $data['parent_id'] > 0) {
            SchemaType::where('id', $data['parent_id'])->where('is_leaf', 1)->update(['is_leaf' => 0]);
        }

        return $updated
            ? response()->json(['status' => true, 'msg' => '更新类型成功'])
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

        foreach ($ids as $id) {
            $type = SchemaType::find($id);
            if ($type) {
                $type->delete();
                $this->removeLeafFlagFromParent($type->parent_id);
            }
        }

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

        foreach ($ids as $id) {
            $type = SchemaType::withTrashed()->find($id);
            if ($type) {
                $type->restore();
                if ($type->parent_id > 0) {
                    SchemaType::where('id', $type->parent_id)->where('is_leaf', 1)->update(['is_leaf' => 0]);
                }
            }
        }

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

        foreach ($ids as $id) {
            $type = SchemaType::withTrashed()->find($id);
            if ($type) {
                $type->forceDelete();
            }
        }

        return response()->json(['status' => true, 'msg' => '彻底删除成功']);
    }

    //---------------- 私有辅助方法 ----------------

    private function appendParentName($item)
    {
        $item->parent_name = $item->parent ? $item->parent->name : '';
        unset($item->parent, $item->parent_id_hidden);
        return $item;
    }

    private function resolveDepth(int $parentId): int
    {
        if ($parentId <= 0) {
            return 0;
        }
        $parent = SchemaType::find($parentId);

        return $parent ? $parent->depth + 1 : 0;
    }

    private function removeLeafFlagFromParent(int $parentId): void
    {
        if ($parentId <= 0) {
            return;
        }
        // 父级下若没有有效子节点则标记为叶子
        $hasChild = SchemaType::where('parent_id', $parentId)->exists();
        if (!$hasChild) {
            SchemaType::where('id', $parentId)->update(['is_leaf' => 1]);
        }
    }

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