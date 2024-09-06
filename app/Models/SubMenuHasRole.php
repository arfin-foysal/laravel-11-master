<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMenuHasRole extends Model
{
    use HasFactory;

    protected $fillable = ['sub_menu_id', 'role_id'];

    
}
