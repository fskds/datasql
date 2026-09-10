<?php

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use App\Models\Setting\Option;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{
    /**
     * 获取选项列表
     */
    public function index(Request $request)
    {
        $settingId = $request->get('setting_id');
        
        if (!$settingId) {
            return response()->json([
                'status' => false,
                'msg' => '缺少 setting_id 参数',
            ]);
        }

        $list = Option::ofSetting($settingId)
            ->sorted()
            ->get();

        return response()->json([
            'status' => true,
            'msg' => '获取成功',
            'data' => $list,
            'total' => $list->count(),
        ]);
    }

    /**
     * 新增选项
     */
    public function store(Request $request)
    {
        
        $data = $request->only([
            'setting_id',
            'label',
            'value',
            'sort',
            'status',
        ]);

        // 验证必填字段
        if (empty($data['setting_id']) || empty($data['label']) || !isset($data['value']) || $data['value'] === '') {
            return response()->json([
                'status' => false,
                'msg' => 'setting_id、label、value 为必填项',
            ]);
        }
        // 默认值
        $data['sort'] = $data['sort'] ?? 0;
        $data['status'] = $data['status'] ?? 1;

        $option = Option::create($data);

        return response()->json([
            'status' => true,
            'msg' => '添加成功',
            'id' => $option->id,
            'data' => $option,
        ]);
    }

    /**
     * 更新选项
     */
    public function update(Request $request, $id)
    {
        $option = Option::find($id);

        if (!$option) {
            return response()->json([
                'status' => false,
                'msg' => '选项不存在',
            ]);
        }

        $data = $request->only([
            'label',
            'value',
            'sort',
            'status',
        ]);

        $option->update($data);

        return response()->json([
            'status' => true,
            'msg' => '更新成功',
            'data' => $option,
        ]);
    }

    /**
     * 删除选项
     */
    public function destroy($id)
    {
        $option = Option::find($id);

        if (!$option) {
            return response()->json([
                'status' => false,
                'msg' => '选项不存在',
            ]);
        }

        $option->delete();

        return response()->json([
            'status' => true,
            'msg' => '删除成功',
        ]);
    }
}