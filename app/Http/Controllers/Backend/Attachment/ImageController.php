<?php

namespace App\Http\Controllers\Backend\Attachment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Attachment\Image;
use App\Models\Attachment\Temp;
use App\Http\Requests\Image\StoreImageRequest;
use App\Http\Requests\Image\UpdateImageRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    /**
     * 图片列表
     */
    public function index(Request $request)
    {
        $current = max(1, (int)$request->get('current', 1));
        $pageSize = max(1, (int)$request->get('pageSize', 10));
        $query = Image::select('id','name','imageUrl','thumb','alt','groupid','size','sort','status');
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->model === 'hasdel') {
            $query->onlyTrashed();
        }
        
        $res  = $query->paginate($pageSize,['*'],'current',$current);
        $result = [
            'status' => true,
            'data' => $res->items(),      // 修复点
            'total' => $res->total(),      // 修复点
            'pageSize' => $res->perPage(),// 修复点
            'current' => $res->currentPage(), // 修复点
        ];
        return response()->json($result);
    }

    /**
     * 创建图片（从temp移动文件到upload）
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // 查找temp记录
        $temp = Temp::where('path', $validated['imageUrl'])->first();

        if (!$temp) {
            return response()->json([
                'success' => false,
                'message' => '临时文件不存在',
            ], 404);
        }

        // 检查temp磁盘文件是否存在
        if (!Storage::disk('temp')->exists($temp->path)) {
            return response()->json([
                'success' => false,
                'message' => '临时文件已丢失',
            ], 404);
        }

        // 目标路径：upload/当天日期/文件名
        $dateDir = date('Ymd');
        $newPath = $dateDir . '/' . $temp->path;

        // 移动文件从temp到upload
        $fileContent = Storage::disk('temp')->get($temp->path);
        Storage::disk('upload')->put($newPath, $fileContent);

        // 删除temp磁盘文件和数据库记录
        Storage::disk('temp')->delete($temp->path);
        $temp->delete();

        // 创建Image记录
        $image = Image::create([
            'name' => $validated['name'] ?? $temp->name,
            'imageUrl' => '/upload/'.$newPath,
            'thumb' => $validated['thumb'] ?? null,
            'alt' => $validated['alt'] ?? $temp->name,
            'groupid' => $validated['groupid'] ?? 'default',
            'size' => $temp->size,
            'sort' => $validated['sort'] ?? 0,
            'status' => $validated['status'] ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => '图片创建成功',
            'data' => $image,
        ], 201);
    }

    /**
     * 图片详情
     */
    public function show(Image $image): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $image,
        ]);
    }

    /**
     * 更新图片
     */
    public function update(UpdateImageRequest $request, Image $image): JsonResponse
    {
        $validated = $request->validated();

        // 如果传了新的url，说明要替换文件
        if (array_key_exists('url', $validated) && $validated['url'] !== null) {
            $newTempPath = $validated['url'];

            // 从数据库url提取文件名
            $dbUrlPath = parse_url($image->url, PHP_URL_PATH);
            $dbFileName = basename($dbUrlPath);

            // url没变化，不需要替换
            if ($newTempPath === $dbFileName) {
                unset($validated['url']);
                $image->update($validated);
                return response()->json([
                    'success' => true,
                    'message' => '图片更新成功',
                    'data' => $image,
                ]);
            }

            // 查找temp记录
            $temp = Temp::where('path', $newTempPath)->first();
            if (!$temp) {
                return response()->json([
                    'success' => false,
                    'message' => '临时文件不存在',
                ], 404);
            }

            if (!Storage::disk('temp')->exists($temp->path)) {
                return response()->json([
                    'success' => false,
                    'message' => '临时文件已丢失',
                ], 404);
            }

            // 用新文件内容覆盖旧文件（文件名不变）
            $fileContent = Storage::disk('temp')->get($temp->path);
            $oldRelativePath = str_replace('/upload/', '', $dbUrlPath);
            Storage::disk('upload')->put($oldRelativePath, $fileContent);

            // 删除temp文件和记录
            Storage::disk('temp')->delete($temp->path);
            $temp->delete();

            // url不变，去掉url避免覆盖
            unset($validated['url']);
        }

        $image->update($validated);

        return response()->json([
            'success' => true,
            'message' => '图片更新成功',
            'data' => $image,
        ]);
    }

    /**
     * 删除图片
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['status' => false, 'msg' => '请选择删除项']);
        }
        if (Image::destroy($ids)) {
            return response()->json(['status' => true, 'msg' => '删除图片成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function restore(Request $request): JsonResponse
    {
        $id = $request->get('ids')[0];
        $image = Image::withTrashed()->find($id);
        if ($image->restore()){
            return response()->json(['status' => true, 'msg' => '恢复图片成功']);
        }
        return response()->json(['status' => false, 'msg' => '未知失败联系管理员']);
    }
    public function force(Request $request): JsonResponse
    {
        //$id = $request->get('ids')[0];
        $ids = $request->get('ids', []);
        if (empty($ids)) return response()->json(['msg'=>'未选择数据'],400);
        $image = Image::withTrashed()->find($ids[0]);
        if (!$image) return response()->json(['msg'=>'记录不存在'],404);
        
        try {
            DB::transaction(function () use ($image) {
                $image->forceDelete();
                if ($image->imageUrl) {
                    $path = ltrim(parse_url($image->imageUrl, PHP_URL_PATH), '/');
                    if (!Storage::disk('upload')->delete($path)) {
                        throw new \Exception('文件删除失败');
                    }
                }
            });
            return response()->json(['msg'=>'删除成功']);
        } catch (\Exception $e) {
            return response()->json(['msg'=>'删除回滚失败','err'=>$e->getMessage()],500);
        }

    }
}
