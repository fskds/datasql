<?php

namespace App\Http\Requests\Acupoint;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAcupointDescRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meridian_code' => 'required|string|max:8',
            'name' => 'required|string|max:32',
            'description' => 'required|string',
            'acupoint_id' => 'nullable|integer|exists:acupoints,id',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'meridian_code.required' => '经脉代码不能为空',
            'name.required' => '穴名不能为空',
            'description.required' => '介绍内容不能为空',
            'acupoint_id.exists' => '关联穴道不存在',
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
