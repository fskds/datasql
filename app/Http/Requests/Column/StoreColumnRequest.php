<?php

namespace App\Http\Requests\Column;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreColumnRequest extends FormRequest
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
            'slug' => 'required|string|max:255|unique:content_columns,slug',
            'description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:255',
            'nav_id' => 'nullable|integer|min:0',
            'cover' => 'nullable|string|max:500',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute 不能为空',
            'name.string' => ':attribute 必须是字符串',
            'name.max' => ':attribute 不能超过255个字符',
            'slug.required' => ':attribute 不能为空',
            'slug.string' => ':attribute 必须是字符串',
            'slug.max' => ':attribute 不能超过255个字符',
            'slug.unique' => ':attribute 已存在',
            'description.string' => ':attribute 必须是字符串',
            'description.max' => ':attribute 不能超过500个字符',
            'keywords.string' => ':attribute 必须是字符串',
            'keywords.max' => ':attribute 不能超过255个字符',
            'nav_id.integer' => ':attribute 必须是整数',
            'nav_id.min' => ':attribute 不能小于0',
            'cover.string' => ':attribute 必须是字符串',
            'cover.max' => ':attribute 不能超过500个字符',
            'sort.integer' => ':attribute 必须是整数',
            'sort.min' => ':attribute 不能小于0',
            'status.integer' => ':attribute 必须是整数',
            'status.in' => ':attribute 必须是0或1',
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
