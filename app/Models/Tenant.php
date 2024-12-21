<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'company',
        'username',
        'number',
        'database',
        'image',
        'is_active',
        'data',
    ];
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'password',
            'company',
            'username',
            'number',
            'database',
            'image',
            'is_active',
            'data',
        ];
    }
}
