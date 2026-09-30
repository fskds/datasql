<?php

namespace App\Http\Requests\Acupoint;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateMeridianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'code' => ['nullable', 'string', 'max:8', Rule::unique('meridians', 'code')->ignore($id)],
            'cn' => 'nullable|string|max:32',
            'pinyin' => 'nullable|string|max:64',
            'type' => 'nullable|string|max:8|in:yin,yang,ren,du',
            'color' => 'nullable|string|max:16',
            'desc' => 'nullable|string',
            'sort' => 'nullable|integer|min:0',
            'status' => 'nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => '经脉代码已存在',
            'type.in' => '类型必须是 yin/yang/ren/du',
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
