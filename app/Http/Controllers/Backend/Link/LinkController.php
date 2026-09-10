<?php
namespace App\Http\Controllers\Backend\Link;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Link\Link;
use App\Http\Requests\Link\StoreLinkRequest;
use App\Http\Requests\Link\UpdateLinkRequest;
use Illuminate\Http\JsonResponse;

class LinkController extends Controller
{
    /**
     * 友情链接列表
     */
    public function index(Request $request)
    {
        $query = Link::select('id','name','url','logo','description','sort','status');
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
     * 创建友链
     */
    public function store(StoreLinkRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (Link::create($validated)) {
            return response()->json(['status' => true,'msg' => '友链创建成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 友链详情
     */
    public function show(): JsonResponse
    {
    }

    /**
     * 更新友链
     */
    public function update(UpdateLinkRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $link = Link::findOrFail($id);
        if ($link->update($validated)) {
            return response()->json(['status' => true, 'msg' => '更新友链成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除友链
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Link::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '删除友链成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $link = Link::withTrashed()->find($id);
        if ($link->restore()){
            return response()->json(['status' => true, 'msg' => '恢复友链成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $link = Link::withTrashed()->find($id);
        if ($link->forceDelete()){
            return response()->json(['status' => true, 'msg' => '强制删除友链成功']);
        }else {
            return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
        }
    }


}
