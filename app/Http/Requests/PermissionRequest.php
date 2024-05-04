<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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

        // if ($this->routeIs('permission.store')) {
        //     return [
        //         'name' => 'required|string|min:2|max:50|unique:permissions,name',
        //     ];
        // }

        // if ($this->routeIs('permission.update')) {
        //     $permissionId = $this->route('id');

        //     return [
        //         'name' => 'required|string|min:2|max:50|unique:permissions,name,'.$permissionId,
        //     ];
        // }

        // if ($this->routeIs('permission.user-permission-assign')) {
        //     return [
        //         'user_id' => 'required|integer',
        //         'permissions' => 'required|array|min:1',
        //     ];
        // }

        // if ($this->routeIs('permission.user-permission-remove')) {
        //     return [
        //         'user_id' => 'required|integer',
        //         'permission_id' => 'required|integer',
        //     ];
        // }

        return [];
    }
}
