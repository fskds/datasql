<?php

namespace App\Http\Requests\Schema;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSchemaTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:schema_types,slug',
            'parent_id' => 'nullable|integer|min:0',
            'is_leaf' => 'nullable|integer|in:0,1',
            'depth' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'example' => 'nullable|string',
            'url' => 'nullable|string|max:255',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '类型名称不能为空',
            'name.string' => '类型名称必须是字符串',
            'name.max' => '类型名称不能超过255个字符',
            'slug.required' => '别名不能为空',
            'slug.string' => '别名必须是字符串',
            'slug.max' => '别名不能超过255个字符',
            'slug.unique' => '别名已存在',
            'parent_id.integer' => '父类型ID必须是整数',
            'is_leaf.in' => 'is_leaf 必须是0或1',
            'depth.integer' => '层级深度必须是整数',
            'url.max' => '链接不能超过255个字符',
            'sort.integer' => '排序必须是整数',
            'sort.min' => '排序不能小于0',
            'status.in' => '状态必须是0或1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}