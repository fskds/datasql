<?php

namespace App\Http\Requests\Comment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_id' => 'required|integer|min:1',
            'content' => 'required|string',
            'author_name' => 'nullable|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'parent_id' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => ':attribute 不能为空',
            'article_id.integer' => ':attribute 必须是整数',
            'content.required' => ':attribute 不能为空',
            'content.string' => ':attribute 必须是字符串',
            'author_name.string' => ':attribute 必须是字符串',
            'author_name.max' => ':attribute 不能超过255个字符',
            'author_email.email' => ':attribute 格式不正确',
            'author_email.max' => ':attribute 不能超过255个字符',
            'parent_id.integer' => ':attribute 必须是整数',
            'status.integer' => ':attribute 必须是整数',
            'status.in' => ':attribute 必须是0或1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}