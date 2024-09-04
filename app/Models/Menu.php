<?php

namespace App\Models;

use App\Http\Traits\OrganizationScopedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;
    use OrganizationScopedTrait;


    protected $fillable = ['organization_id', 'name','role_id', 'description', 'icon', 'url', 'order', 'created_by', 'is_active'];

    public function subMenus()
    {
        return $this->hasMany(SubMenu::class, 'menu_id', 'id');
    }
}
