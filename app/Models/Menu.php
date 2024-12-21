<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [ 'name', 'description', 'icon', 'url', 'order', 'created_by', 'is_active'];

    public function subMenus()
    {
        return $this->hasMany(SubMenu::class, 'menu_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'menu_has_roles', 'menu_id', 'role_id');
    }
}
