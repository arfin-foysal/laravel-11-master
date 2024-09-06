<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => ['required', 'string','unique:categories,name'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'integer'],
        ];
    }
}
