<?php

namespace App\Http\Requests\Nav;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNavRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'pId' => 'sometimes|nullable|integer|min:0',
            'path' => 'sometimes|nullable|string|max:255',
            'groupId' => 'sometimes',
            'sort' => 'sometimes|nullable|integer|min:0',
            'status' => 'sometimes|nullable|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute 不能为空',
            'name.string' => ':attribute 必须是字符串',
            'name.max' => ':attribute 不能超过255个字符',
            'pid.integer' => ':attribute 必须是整数',
            'pid.min' => ':attribute 不能小于0',
            'path.string' => ':attribute 必须是字符串',
            'path.max' => ':attribute 不能超过255个字符',
            'sort.integer' => ':attribute 必须是整数',
            'sort.min' => ':attribute 不能小于0',
            'status.integer' => ':attribute 必须是整数',
            'status.in' => ':attribute 必须是0或1',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg' => $validator->errors()->first(),
            //'errors' => $validator->errors(),
        ], 422));
    }
}
