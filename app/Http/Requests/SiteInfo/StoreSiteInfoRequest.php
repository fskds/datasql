<?php

namespace App\Http\Requests\SiteInfo;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSiteInfoRequest extends FormRequest
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
            'varname' => 'required|string|max:255|unique:content_site_infos,varname',
            'info' => 'nullable|string',
            'groupid' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'value' => 'nullable|string',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'varname.required' => ':attribute 不能为空',
            'varname.string' => ':attribute 必须是字符串',
            'varname.max' => ':attribute 不能超过255个字符',
            'varname.unique' => ':attribute 已存在',
            'info.string' => ':attribute 必须是字符串',
            'groupid.string' => ':attribute 必须是字符串',
            'groupid.max' => ':attribute 不能超过255个字符',
            'type.string' => ':attribute 必须是字符串',
            'type.max' => ':attribute 不能超过255个字符',
            'value.string' => ':attribute 必须是字符串',
            'status.integer' => ':attribute 必须是整数',
            'status.in' => ':attribute 必须是0或1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}
