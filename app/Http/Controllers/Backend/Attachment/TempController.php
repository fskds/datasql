<?php

namespace App\Http\Controllers\Backend\Attachment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Temp\StoreTempRequest;
use App\Models\Attachment\Temp;
use App\Models\Setting\SiteInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TempController extends Controller
{
    /**
     * 临时文件列表
     */
    public function index(): JsonResponse
    {
        $temps = Temp::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $temps,
        ]);
    }

    /**
     * 上传文件到临时目录
     */
    public function store(StoreTempRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $sizeByte = $file->getSize();
        $sizeKb = $sizeByte / 1024;

        // 获取允许的后缀
        $allowSuffix = SiteInfo::getValue('upload_suffix', 'jpg,jpeg,png,gif,bmp,webp,svg,ico,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,7z,mp4,mp3,wav,avi,mov');
        $allowedExtensions = explode(',', $allowSuffix);
        $allowedExtensions = array_map('trim', $allowedExtensions);
        $allowedExtensions = array_map('strtolower', $allowedExtensions);

        // 验证后缀
        $originalName = $file->getClientOriginalName();
        $originalExtension = strtolower($file->getClientOriginalExtension());

        if (!in_array($originalExtension, $allowedExtensions)) {
            return response()->json([
                'success' => false,
                'message' => '不允许上传该文件类型，允许的类型：' . $allowSuffix,
            ], 422);
        }

        // 重新命名
        $newName = date('Ymd') . uniqid() . '.' . $originalExtension;

        // 保存到temp磁盘
        $path = $file->storeAs('', $newName, 'temp');

        // 写入数据库
        $temp = Temp::create([
            'name' => $originalName,
            'path' => $path,
            'size' => $sizeKb,
        ]);

        return response()->json([
            'success' => true,
            'message' => '文件上传成功',
            'data' => $temp,
        ], 201);
    }

    /**
     * 临时文件详情
     */
    public function show(Temp $temp): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $temp,
        ]);
    }

    /**
     * 删除临时文件
     */
    public function destroy(Temp $temp): JsonResponse
    {
        // 同时删除磁盘文件
        Storage::disk('temp')->delete($temp->path);

        $temp->delete();

        return response()->json([
            'success' => true,
            'message' => '临时文件删除成功',
        ]);
    }
}
