<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class CustomRole extends BaseRole
{
    use HasFactory;

    protected $fillable = [
       'id', 'name', 'guard_name',
    ];

    public function getMenus ()
    {
        return $this->hasMany(Menu::class, 'role_id', 'id')
            ->select('id','organization_id', 'name','description', 'role_id', 'url', 'icon','order','is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->with(['subMenus' => function ($query) {
                $query->select('id', 'menu_id', 'organization_id', 'role_id', 'name', 'description', 'icon', 'url', 'order', 'is_active')
                    ->where('is_active', true)
                    ->orderBy('order');
            }]);
    }
}
