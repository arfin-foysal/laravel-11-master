<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuHasRole extends Model
{
    use HasFactory;

    protected $fillable = ['menu_id', 'role_id'];
}
