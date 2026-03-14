<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipEvent extends Model
{
protected $casts = [
    'meta_json' => 'array',
];

public function customer()
{
    return $this->belongsTo(Customer::class);
}

public function fromTier()
{
    return $this->belongsTo(MembershipTier::class, 'from_tier_id');
}

public function toTier()
{
    return $this->belongsTo(MembershipTier::class, 'to_tier_id');
}
}
