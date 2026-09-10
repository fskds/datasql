<?php

namespace App\Http\Requests\Section;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSectionRequest extends FormRequest
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
            'column_id' => 'sometimes|integer',
            'nameEn' => 'sometimes|nullable|string|max:255',
            'title' => 'sometimes|nullable|string|max:255',
            'subtitle' => 'sometimes|nullable|string|max:255',
            'h2' => 'sometimes|nullable|string|max:255',
            'span' => 'sometimes|nullable|string|max:255',
            'html' => 'sometimes|nullable|string',
            'css' => 'sometimes|nullable|string',
            'js' => 'sometimes|nullable|string',
            'cover' => 'sometimes|nullable|string|max:500',
            'scw' => 'sometimes|nullable|string|max:255',
            'stc' => 'sometimes|nullable|string|max:255',
            'sbi' => 'sometimes|nullable|string|max:500',
            'sort' => 'sometimes|nullable|integer|min:0',
            'status' => 'sometimes|nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'column_id.integer' => ':attribute 必须是整数',
            'nameEn.string' => ':attribute 必须是字符串',
            'nameEn.max' => ':attribute 不能超过255个字符',
            'title.string' => ':attribute 必须是字符串',
            'title.max' => ':attribute 不能超过255个字符',
            'subtitle.string' => ':attribute 必须是字符串',
            'subtitle.max' => ':attribute 不能超过255个字符',
            'h2.string' => ':attribute 必须是字符串',
            'h2.max' => ':attribute 不能超过255个字符',
            'span.string' => ':attribute 必须是字符串',
            'span.max' => ':attribute 不能超过255个字符',
            'html.string' => ':attribute 必须是字符串',
            'css.string' => ':attribute 必须是字符串',
            'js.string' => ':attribute 必须是字符串',
            'cover.string' => ':attribute 必须是字符串',
            'cover.max' => ':attribute 不能超过500个字符',
            'scw.string' => ':attribute 必须是字符串',
            'scw.max' => ':attribute 不能超过255个字符',
            'stc.string' => ':attribute 必须是字符串',
            'stc.max' => ':attribute 不能超过255个字符',
            'sbi.string' => ':attribute 必须是字符串',
            'sbi.max' => ':attribute 不能超过500个字符',
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
