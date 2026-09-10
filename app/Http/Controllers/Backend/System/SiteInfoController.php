<?php
namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting\SiteInfo;
use App\Http\Requests\SiteInfo\StoreSiteInfoRequest;
use App\Http\Requests\SiteInfo\UpdateSiteInfoRequest;
use Illuminate\Http\JsonResponse;

class SiteInfoController extends Controller
{
    /**
     * 配置列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = SiteInfo::query();

        if ($request->has('groupid')) {
            $query->byGroup($request->groupid);
        }

        $configs = $query->orderBy('groupid')->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $configs,
        ]);
        

        $query = SiteInfo::query();
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->model === 'hasdel') {
            $query->onlyTrashed();
        }
        
        $res  = $query;
        $result = [
            'status' => true,
            'data' => $res->items(),      // 修复点
            'total' => $res->total(),      // 修复点
            'pageSize' => $res->perPage(),// 修复点
            'current' => $res->currentPage(), // 修复点
        ];
        return response()->json('$result');
        
        
    }

    /**
     * 按分组获取配置
     */
    public function group(string $groupid): JsonResponse
    {
        $configs = SiteInfo::getGroup($groupid);

        return response()->json([
            'success' => true,
            'data' => $configs,
        ]);
    }

    /**
     * 创建配置
     */
    public function store(StoreSiteInfoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (SiteInfo::create($validated)) {
            return response()->json(['status' => true,'msg' => '成功设置菜单']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 配置详情
     */
    public function show(SiteInfo $siteInfo): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $siteInfo,
        ]);
    }

    /**
     * 更新配置
     */
    public function update(UpdateSiteInfoRequest $request, $id ): JsonResponse
    {
        $validated = $request->validated();
        $siteinfo = SiteInfo::findOrFail($id);
        if ($siteinfo->update($validated)) {
            return response()->json(['status' => true, 'msg' => '更新设置成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除配置
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (SiteInfo::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '删除设置成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $siteinfo = SiteInfo::withTrashed()->find($id);
        if ($siteinfo->restore()){
            return response()->json(['status' => true, 'msg' => '恢复设置成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $siteinfo = SiteInfo::withTrashed()->find($id);
        if ($siteinfo->forceDelete()){
            return response()->json(['status' => true, 'msg' => '强制删除设置成功']);
        }else {
            return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
        }
    }
}
