<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Schema\SchemaType;
use App\Models\Schema\SchemaProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchemaController extends Controller
{
    /**
     * 允许访问的来源域名白名单（仅这些来源可调用 Schema 前端接口）
     */
    private const ALLOWED_ORIGINS = [
        'schema.cnsesi.com',
        // 本地联调来源
        'localhost',
        '127.0.0.1',
    ];

    /**
     * 校验请求来源域名，不在白名单内返回 403
     */
    private function authorizeOrigin(Request $request): ?JsonResponse
    {
        $origin = $request->header('Origin') ?? $request->header('Referer');
        if (!$origin) {
            // 同源/服务端等无来源场景视为可访问
            return null;
        }

        $host = strtolower((string) parse_url($origin, PHP_URL_HOST));
        if (in_array($host, self::ALLOWED_ORIGINS, true)) {
            return null;
        }

        return response()->json([
            'status' => false,
            'msg'    => '来源域名未授权：' . $host,
            'data'   => null,
        ], 403);
    }

    /**
     * 统一成功响应
     */
    private function success($data = null, string $msg = 'success'): JsonResponse
    {
        return response()->json([
            'status' => true,
            'msg'    => $msg,
            'data'   => $data,
        ]);
    }

    /**
     * 统一失败响应
     */
    private function error(string $msg = 'error', $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => false,
            'msg'    => $msg,
            'data'   => $data,
        ], $code);
    }

    /**
     * Schema 类型平铺列表（id/name/parent/depth/isLeaf/description）
     * GET /api/schema/types
     */
    public function schemaTypes(Request $request): JsonResponse
    {
        $denied = $this->authorizeOrigin($request);
        if ($denied) return $denied;

        $types = SchemaType::with('parent:id,name')
            ->where('status', 1)
            ->orderBy('sort')
            ->get();

        $typeNodes = $types->map(function ($t) {
            return [
                'id'          => (int) $t->id,
                'name'        => $t->name,
                'parent'      => $t->parent ? $t->parent->name : null,
                'depth'       => (int) $t->depth,
                'isLeaf'      => (int) $t->is_leaf === 1,
                'description' => $t->description ?? '',
                'example'     => $t->example ?? '',
            ];
        })->values()->toArray();

        return $this->success($typeNodes);
    }

    /**
     * Schema 总览统计（首页轻量数据，避免拉取全部属性）
     * GET /api/schema/summary
     */
    public function schemaSummary(Request $request): JsonResponse
    {
        $denied = $this->authorizeOrigin($request);
        if ($denied) return $denied;

        return $this->success([
            'typeCount'  => SchemaType::where('status', 1)->count(),
            'totalProps' => SchemaProperty::where('status', 1)->count(),
        ]);
    }

    /**
     * Schema 类型树（含属性详情）
     * GET /api/schema/tree
     */
    public function schemaTree(Request $request): JsonResponse
    {
        $denied = $this->authorizeOrigin($request);
        if ($denied) return $denied;

        $types = SchemaType::where('status', 1)->orderBy('sort')->get()->keyBy('id');

        $totalNodes = $types->count();
        $typesWithDetail = 0;
        $totalProps = 0;

        // 取出所有属性，按 type_id 分组
        $allProps = SchemaProperty::where('status', 1)->orderBy('sort')->get();
        $typeDirectProps = [];
        foreach ($allProps as $p) {
            $typeDirectProps[$p->type_id][] = [
                'prop'    => $p->name,
                'expType' => $p->expected_type,
                'desc'    => $p->description,
            ];
        }

        // 每个类型只显示自身属性
        $typeAllProps = [];
        foreach ($types as $type) {
            $own = $typeDirectProps[$type->id] ?? [];
            $typeAllProps[$type->id] = $own;
            if (count($own) > 0) $typesWithDetail++;
            $totalProps += count($own);
        }

        $roots = $this->buildSchemaTree($types, 0, $typeAllProps);

        return $this->success([
            'totalNodes'           => $totalNodes,
            'totalTypesWithDetail' => $typesWithDetail,
            'totalProps'           => $totalProps,
            'roots'                => $roots,
        ]);
    }

    /**
     * 递归构建 Schema 树
     */
    private function buildSchemaTree($types, $parentId, $typeAllProps): array
    {
        $result = [];
        foreach ($types as $type) {
            if ($type->parent_id != $parentId) continue;

            $node = [
                'type'   => $type->name,
                'desc'   => $type->description ?? '',
                'isLeaf' => $type->is_leaf == 1,
                'detail' => $typeAllProps[$type->id] ?? [],
            ];

            $children = $this->buildSchemaTree($types, $type->id, $typeAllProps);
            if (count($children) > 0) {
                $node['children'] = $children;
            } else {
                $node['children'] = [];
            }

            $result[] = $node;
        }
        return $result;
    }

    /**
     * 按类型 ID 获取该类型自身的属性 + 继承链上的属性（按需加载，只返回当前需要）
     * GET /api/schema/properties/{id}
     */
    public function schemaPropertiesForType(Request $request, int $id): JsonResponse
    {
        $denied = $this->authorizeOrigin($request);
        if ($denied) return $denied;

        $type = SchemaType::where('id', $id)->where('status', 1)->first();
        if (!$type) {
            return $this->error('类型不存在');
        }

        // 收集继承链（根在前、自身在后）的 id 与 name
        $chainIds = [];
        $chainNames = [];
        $cur = SchemaType::where('id', $type->id)->first();
        while ($cur) {
            array_unshift($chainIds, $cur->id);
            array_unshift($chainNames, $cur->name);
            $cur = $cur->parent_id
                ? SchemaType::where('id', $cur->parent_id)->first()
                : null;
        }

        $propsById = SchemaProperty::where('status', 1)
            ->whereIn('type_id', $chainIds)
            ->orderBy('sort')
            ->get()
            ->groupBy('type_id');

        $groups = [];
        foreach ($chainNames as $i => $name) {
            $chainId = $chainIds[$i];
            $list = ($propsById->get($chainId) ?? collect())->map(function ($p) {
                return [
                    'name'         => $p->name,
                    'expectedType' => $p->expected_type ?? '',
                    'description'  => $p->description ?? '',
                ];
            })->values()->toArray();
            $groups[] = ['origin' => $name, 'props' => $list];
        }

        return $this->success([
            'name'        => $type->name,
            'description' => $type->description ?? '',
            'example'     => $type->example ?? '',
            'groups'      => $groups,
        ]);
    }
}