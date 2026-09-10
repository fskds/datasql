<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|string|max:255|unique:admin_users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_users,email',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'password' => 'required|string|min:6',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => '用户名不能为空',
            'username.unique' => '用户名已存在',
            'name.required' => '姓名不能为空',
            'email.required' => '邮箱不能为空',
            'email.email' => '请输入有效的邮箱地址',
            'email.unique' => '邮箱已存在',
            'mobile.required' => '电话不能为空',
            'mobile.regex' => '请输入有效的手机号码',
            'password.required' => '密码不能为空',
            'password.min' => '密码长度不能少于6位',
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