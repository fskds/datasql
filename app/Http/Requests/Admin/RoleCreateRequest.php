<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:admin_roles,name',
            'code' => 'required|string|max:255|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/|unique:admin_roles,code',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '角色名称不能为空',
            'name.unique' => '角色名称已存在',
            'code.required' => '角色编码不能为空',
            'code.regex' => '角色编码只能包含英文字母、数字和下划线，且以字母开头',
            'code.unique' => '角色编码已存在',
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