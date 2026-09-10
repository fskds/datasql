<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PermissionCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'display_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z][a-zA-Z0-9_.:]*$/|unique:admin_permissions,name',
            'type' => 'nullable|string|in:menu,button,api',
            'path' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:200',
            'status' => 'nullable|boolean',
            'pid' => ['nullable', 'integer', function ($attribute, $value, $fail) {
                if ((int) $value !== 0 && !\Illuminate\Support\Facades\DB::table('admin_permissions')->where('id', $value)->exists()) {
                    $fail('父级权限不存在');
                }
            }],
        ];
    }

    public function messages()
    {
        return [
            'display_name.required' => '权限名称不能为空',
            'name.required' => '权限编码不能为空',
            'name.regex' => '权限编码只能包含英文字母、数字、点、冒号和下划线，且以字母开头',
            'name.unique' => '权限编码已存在',
            'type.in' => '权限类型不合法',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new HttpResponseException(response()->json([
            'status' => false,
            'msg' => $validator->errors()->first(),
        ], 200)));
    }
}