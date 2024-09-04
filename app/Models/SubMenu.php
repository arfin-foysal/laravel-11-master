<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class SubMenu extends Model
{
    use HasFactory;
    use SoftDeletes;
    use OrganizationScopedTrait;


    protected $fillable = ['menu_id', 'organization_id', 'name', 'description', 'icon', 'url', 'order', 'created_by', 'is_active'];


    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sub_menu_has_roles', 'sub_menu_id', 'role_id');
    }
}
