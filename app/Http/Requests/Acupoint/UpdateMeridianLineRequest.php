<?php

namespace App\Http\Requests\Acupoint;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMeridianLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meridian_id' => 'nullable|integer|exists:meridians,id',
            'meridian_code' => 'nullable|string|max:8',
            'side' => 'nullable|string|max:2|in:L,R,C',
            'color' => 'nullable|string|max:16',
            'obj' => 'nullable|string|max:128',
            'pos_x' => 'nullable|numeric',
            'pos_y' => 'nullable|numeric',
            'pos_z' => 'nullable|numeric',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'meridian_id.exists' => '所属经脉不存在',
            'side.in' => '侧别必须是 L/R/C',
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
