<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class CustomRole extends BaseRole
{
    use HasFactory;

    protected $fillable = [
        'name', 'guard_name',
    ];
}
