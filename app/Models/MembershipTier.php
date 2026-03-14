<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
protected $casts = [
    'benefits_json' => 'array',
    'is_active' => 'boolean',
];

public function memberships()
{
    return $this->hasMany(CustomerMembership::class, 'current_tier_id');
}

protected $fillable = ['name', 'rank', 'min_spend', 'benefits_json', 'is_active'];
}
