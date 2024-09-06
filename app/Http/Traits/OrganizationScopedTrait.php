<?php

namespace App\Http\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

trait OrganizationScopedTrait
{
    public static function bootOrganizationScopedTrait()
    {
        // Automatically insert organization_id and created_by on creating
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->organization_id = auth()->user()->organization_id;
                $model->created_by = auth()->id();
            }
        });

        // Apply global scope to filter by organization_id
        // static::addGlobalScope('organization', function (Builder $builder) {
        //     if (auth()->check() && in_array(auth()->user()->role, [ 'system-admin', 'super-admin'])) {
        //         return;
        //     }
        //     $builder->where('organization_id', auth()->user()->organization_id);
        // });
    }

    // Optional: A query scope to allow custom queries based on organization_id
    public function scopeForOrganization($query, $tableName)
    {
        if (auth()->check() && in_array(auth()->user()->role, ['system-admin', 'super-admin'])) {
            return $query;
        }

        return $query->where($tableName.'.organization_id', auth()->user()->organization_id);
    }
}
