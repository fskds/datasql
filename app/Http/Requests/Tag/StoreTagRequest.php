<?php

namespace App\Http\Requests\Tag;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTagRequest extends FormRequest
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
            'slug' => 'required|string|max:255|unique:content_tags,slug',
            'description' => 'nullable|string|max:500',
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
