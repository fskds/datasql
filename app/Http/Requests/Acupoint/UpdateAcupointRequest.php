<?php

namespace App\Http\Requests\Acupoint;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateAcupointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'point_id' => ['nullable', 'string', 'max:32', Rule::unique('acupoints', 'point_id')->ignore($id)],
            'meridian_id' => 'nullable|integer|exists:meridians,id',
            'meridian_code' => 'nullable|string|max:8',
            'side' => 'nullable|string|max:2|in:L,R,C',
            'seq' => 'nullable|integer|min:0',
            'name' => 'nullable|string|max:32',
            'meridian_cn' => 'nullable|string|max:32',
            'color' => 'nullable|string|max:16',
            'obj' => 'nullable|string|max:128',
            'scale_x' => 'nullable|numeric',
            'scale_y' => 'nullable|numeric',
            'scale_z' => 'nullable|numeric',
            'x' => 'nullable|numeric',
            'y' => 'nullable|numeric',
            'z' => 'nullable|numeric',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'point_id.unique' => '穴道ID已存在',
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
