<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'registered_at'
    ];

public function invoices()
{
    return $this->hasMany(Invoice::class);
}

public function membership()
{
    return $this->hasOne(CustomerMembership::class);
}

public function membershipEvents()
{
    return $this->hasMany(MembershipEvent::class);
}
}
