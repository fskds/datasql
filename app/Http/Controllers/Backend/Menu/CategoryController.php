<?php
namespace App\Http\Controllers\Backend\Menu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\menu\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * 分类列表
     */
    public function index(Request $request): JsonResponse
    {
        $model = $request->get('model');
        $query = Category::orderBy('sort', 'asc');
        if(!empty($request->get('name'))){
            $model = 'search';
        }
        switch (strtolower($model)) {
            case 'hasdel':
                $deleted = (clone $query)->onlyTrashed()->get();
                $all = (clone $query)->withTrashed()->get();
                
                $data = $deleted->filter(function ($item) use ($deleted, $all) {
                    $currentPid = $item->pId;
                    // 循环向上检查所有祖先
                    while ($currentPid != 0) {
                        // 检查当前父ID是否在已删除列表里（集合正确用法）
                        if ($deleted->contains('id', $currentPid)) {
                            return false; // 祖先被删 → 不显示当前节点
                        }
                        // 继续往上找爷爷节点
                        $parent = $all->firstWhere('id', $currentPid);
                        $currentPid = $parent ? $parent->pId : 0;
                    }
                    return true; // 祖先都没删 → 显示当前顶级节点
                });
                foreach ($data as $value) {
                    $ar = $this->getCategoryTree($all, $value->id);
                    if (count($ar) > 0) {
                        $value->children = $ar;
                    }
                }
                $res = $data->values(); 
                break;
            case 'search':
                $res = $query->where('name','like', '%'.$request->get('name').'%')->get();
                break;
            default:
                $data = $query->get();
                $res = $this->getCategoryTree($data, 0);
                break;
        }

        return response()->json([ 'data' => $res, 'status' => true, ]);
    }
    /**
     * 创建分类
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if (Category::create($validated)) {
            return response()->json(['status' => true,'msg' => '成功添加菜单']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 分类详情
     */
    public function show(): JsonResponse
    {
        
    }

    /**
     * 更新分类
     */
    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $category = Category::findOrFail($id);
        if ($category->update($validated)) {
            return response()->json(['status' => true, 'msg' => '成功更新菜单']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    /**
     * 删除分类
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Category::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '成功删除菜单']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $category = Category::withTrashed()->find($id);
        if ($category->restore()){
            return response()->json(['status' => true, 'msg' => '成功恢复菜单']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function force(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $category = Category::withTrashed()->find($id);
        $ccategory = Category::withTrashed()->where('pId', '=' , $category->id)->first();
        if(empty($ccategory))
        {
            if ($category->forceDelete()){
                return response()->json(['status' => true, 'msg' => '成功强制删除菜单']);
            }else {
                return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
            }
        }else{
            return response()->json(['status' => false, 'msg' => '子菜单不为空']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }

    public function data(Request $request)
    {
        $name = $request->get('name');
        $query = new Category;
        if(!empty($name)){
            $query = $query->where('name','like', '%'.$name.'%')->take(10)->get();
            foreach ($query as $value){
                $ar['label'] = $value['name'];
                $ar['value'] = $value['id'];
                $res[] = $ar;
            }
        }else{
            $query = $query->get();
            $res = $this->getCategoryDataTree($query, 0);
            $z['label'] = '顶级权限';
            $z['value'] = 0 ;
            array_unshift($res, $z);
        }
        if(count($query) <= 0){
            return response()->json([['label'=>'顶级权限','value'=>'0']]);
        }else{
            return response()->json(['status' => true, 'data' => $res]);
        }

    }
    private function getCategoryDataTree($data,$pid){
        $arr =[];
        foreach ($data as $value){
            if($value['pId'] == $pid){
                $ar['label'] = $value['name'];
                $ar['value'] = $value['id'];
                $ar['children'] = $this->getCategoryDataTree($data,$value['id']);
                if (count($ar['children']) <= 0) {
                    unset($ar['children']);
                }
                $arr[] = $ar;
            }
        }
        return $arr;
    }
    /**
     * 树形转换
     */
    private function getCategoryTree($data,$pid ){
        $arr =[];
        foreach ($data as $value){
            if($value['pId'] == $pid){
                $value['children'] = $this->getCategoryTree($data,$value['id']);
                if (count($value['children']) <= 0) {
                    unset($value['children']);
                }
                $arr[] = $value;
            }
        }
        return $arr;
    }
}
