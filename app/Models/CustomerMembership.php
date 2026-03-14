<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMembership extends Model
{
protected $casts = [
    'evaluated_at' => 'datetime',
    'downgrade_eligible_at' => 'datetime',
    'override_until' => 'datetime',
];

public function customer()
{
    return $this->belongsTo(Customer::class);
}

public function currentTier()
{
    return $this->belongsTo(MembershipTier::class, 'current_tier_id');
}

public function downgradeCandidateTier()
{
    return $this->belongsTo(MembershipTier::class, 'downgrade_candidate_tier_id');
}

public function overrideTier()
{
    return $this->belongsTo(MembershipTier::class, 'override_tier_id');
}

protected $fillable = [
    'customer_id',
    'current_tier_id',
    'window_spend',
    'evaluated_at',
    'downgrade_candidate_tier_id',
    'downgrade_eligible_at',
    'override_tier_id',
    'override_until',
    'override_reason',
];
}
