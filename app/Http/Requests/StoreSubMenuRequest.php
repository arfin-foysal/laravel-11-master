<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubMenuRequest extends FormRequest
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
            'name' => 'required|string|max:55|unique:sub_menus,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'role_ids' => 'nullable|array',
            'url' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|integer',
            'menu_id' => 'required|integer',

        ];
    }
}
