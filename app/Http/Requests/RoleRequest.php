<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
        // if ($this->routeIs('role.store')) {
        //     return [
        //         'name' => 'required|string|max:55|unique:roles,name',
        //         'permissions' => 'nullable|array',
        //     ];
        // }

        // if ($this->routeIs('role.update')) {
        //     $roleId = $this->route('id');

        //     return [
        //         'name' => 'required|string|max:55|unique:roles,name,'.$roleId,
        //         'permissions' => 'nullable|array',
        //     ];
        // }

        // if ($this->routeIs('role.assign-user-role')) {
        //     return [
        //         'user_id' => 'required|integer',
        //         'role_id' => 'required|integer',
        //     ];
        // }

        // if ($this->routeIs('role.remove-user-role')) {
        //     return [
        //         'user_id' => 'required|integer',
        //         'role_id' => 'required|integer',
        //     ];
        // }

        // if ($this->routeIs('role.assign-role-permission')) {
        //     return [
        //         'role_id' => 'required|integer',
        //         'permissions' => 'required|array|min:1',
        //     ];
        // }

        // if ($this->routeIs('role.remove-role-permission')) {
        //     return [
        //         'role_id' => 'required|integer',
        //         'permission_id' => 'required|integer',
        //     ];
        // }

        return [];
    }
}
