<?php

namespace App\Http\Controllers\Backend\Menu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu\Tag;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * 标签列表
     */
    public function index(Request $request)
    {
        $query = Tag::select('id','name','slug','description','sort','status');
        if(!empty($request->get('name'))){
            $query = $query->where('name','like', '%'.$request->get('name').'%');
        }
        if(!empty($request->get('model')) && $request->get('model')==='hasdel'){
            $query = $query->onlyTrashed();
        }
        $res  = $query->paginate($request->get('limit', $request->get('pageSize')))->toArray();
        $result = [
            'status' => true,
            'data' => $res['data'],
            'total' => $res['total'],
            'pageSize' => $res['last_page'],
            'current' => $res['current_page'],
          ];
        return response()->json($result);
    }

    /**
     * 创建标签
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (Tag::create($validated)) {
            return response()->json(['status' => true,'msg' => '标签创建成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 标签详情
     */
    public function show(): JsonResponse
    {
    }

    /**
     * 更新标签
     */
    public function update(UpdateTagRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $tag = Tag::findOrFail($id);
        if ($tag->update($validated)) {
            return response()->json(['status' => true, 'msg' => '更新标签成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除标签
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Tag::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '删除标签成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $tag = Tag::withTrashed()->find($id);
        if ($tag->restore()){
            return response()->json(['status' => true, 'msg' => '恢复标签成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $tag = Tag::withTrashed()->find($id);
        if ($tag->forceDelete()){
            return response()->json(['status' => true, 'msg' => '强制删除标签成功']);
        }else {
            return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
        }
    }
}
