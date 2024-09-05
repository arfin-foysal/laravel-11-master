<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubMenuRequest extends FormRequest
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
            'name' => 'required|string|max:55|unique:sub_menus,name,' .  $this->route('sub_menu'),
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'url' => 'nullable|string',
            'role_ids' => 'nullable|array',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'menu_id' => 'required|integer',
        ];
    }
}
