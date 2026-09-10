<?php

namespace App\Http\Requests\Schema;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSchemaPropertyRequest extends FormRequest
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
            'type_id' => 'sometimes|required|integer|exists:schema_types,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'expected_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|string|max:255',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'type_id.required' => '所属类型不能为空',
            'type_id.exists' => '所属类型不存在',
            'name.required' => '属性名称不能为空',
            'name.string' => '属性名称必须是字符串',
            'name.max' => '属性名称不能超过255个字符',
            'slug.max' => '别名不能超过255个字符',
            'expected_type.max' => '期望类型不能超过255个字符',
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