<?php

namespace App\Models\Concerns;

use App\Support\CurrentOrganization;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $orgId = app(CurrentOrganization::class)->id;
            if ($orgId) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', $orgId);
            }
        });

        static::creating(function ($model) {
            if (!$model->organization_id) {
                $orgId = app(CurrentOrganization::class)->id;
                if ($orgId) {
                    $model->organization_id = $orgId;
                }
            }
        });
    }
}