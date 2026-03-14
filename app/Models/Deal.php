<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToOrganization;

class Deal extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'title',
        'value',
        'currency',
        'status',
        'organization_id',
    ];
}