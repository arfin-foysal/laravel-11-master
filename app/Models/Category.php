<?php

namespace App\Models;

use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HelperTrait;


    protected $fillable = ['parent_id','image','name', 'description', 'is_active', 'created_by', 'organization_id'];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
