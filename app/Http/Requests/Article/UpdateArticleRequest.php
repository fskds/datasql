<?php

namespace App\Http\Requests\Article;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateArticleRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:content_articles,slug,' . $this->route('id'),
            'content' => 'sometimes|required|string',
            'excerpt' => 'sometimes|nullable|string|max:500',
            'cover' => 'sometimes|nullable|string|max:500',
            'keywords' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:500',
            'code' => 'sometimes|nullable|string',
            'flag_s' => 'sometimes|nullable|integer|in:0,1',
            'flag_c' => 'sometimes|nullable|integer|in:0,1',
            'flag_o' => 'sometimes|nullable|integer|in:0,1',
            'category_id' => 'sometimes|nullable|integer|min:0',
            'author_id' => 'sometimes|nullable|integer|min:0',
            'sort' => 'sometimes|nullable|integer|min:0',
            'status' => 'sometimes|nullable|integer|in:0,1',
            'published_at' => 'sometimes|nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => ':attribute 不能为空',
            'title.string' => ':attribute 必须是字符串',
            'title.max' => ':attribute 不能超过255个字符',
            'slug.required' => ':attribute 不能为空',
            'slug.string' => ':attribute 必须是字符串',
            'slug.max' => ':attribute 不能超过255个字符',
            'slug.unique' => ':attribute 已存在',
            'content.required' => ':attribute 不能为空',
            'content.string' => ':attribute 必须是字符串',
            'excerpt.string' => ':attribute 必须是字符串',
            'excerpt.max' => ':attribute 不能超过500个字符',
            'cover.string' => ':attribute 必须是字符串',
            'cover.max' => ':attribute 不能超过500个字符',
            'keywords.string' => ':attribute 必须是字符串',
            'keywords.max' => ':attribute 不能超过255个字符',
            'description.string' => ':attribute 必须是字符串',
            'description.max' => ':attribute 不能超过500个字符',
            'code.string' => ':attribute 必须是字符串',
            'flag_s.integer' => ':attribute 必须是整数',
            'flag_s.in' => ':attribute 必须是0或1',
            'flag_c.integer' => ':attribute 必须是整数',
            'flag_c.in' => ':attribute 必须是0或1',
            'flag_o.integer' => ':attribute 必须是整数',
            'flag_o.in' => ':attribute 必须是0或1',
            'category_id.integer' => ':attribute 必须是整数',
            'category_id.min' => ':attribute 不能小于0',
            'author_id.integer' => ':attribute 必须是整数',
            'author_id.min' => ':attribute 不能小于0',
            'sort.integer' => ':attribute 必须是整数',
            'sort.min' => ':attribute 不能小于0',
            'status.integer' => ':attribute 必须是整数',
            'status.in' => ':attribute 必须是0或1',
            'published_at.date' => ':attribute 日期格式不正确',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg' => $validator->errors()->first(),
            'errors' => $validator->errors(),
        ], 422));
    }
}
