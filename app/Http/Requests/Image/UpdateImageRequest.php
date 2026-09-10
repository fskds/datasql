<?php

namespace App\Http\Requests\Image;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateImageRequest extends FormRequest
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
            'url' => 'sometimes|nullable|string|max:500',
            'name' => 'sometimes|nullable|string|max:255',
            'thumb' => 'sometimes|nullable|string|max:500',
            'alt' => 'sometimes|nullable|string|max:255',
            'groupid' => 'sometimes|nullable|string|max:255',
            'sort' => 'sometimes|nullable|integer|min:0',
            'status' => 'sometimes|nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'url.string' => ':attribute 必须是字符串',
            'url.max' => ':attribute 不能超过500个字符',
            'name.string' => ':attribute 必须是字符串',
            'name.max' => ':attribute 不能超过255个字符',
            'thumb.string' => ':attribute 必须是字符串',
            'thumb.max' => ':attribute 不能超过500个字符',
            'alt.string' => ':attribute 必须是字符串',
            'alt.max' => ':attribute 不能超过255个字符',
            'groupid.string' => ':attribute 必须是字符串',
            'groupid.max' => ':attribute 不能超过255个字符',
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
