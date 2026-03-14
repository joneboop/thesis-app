<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'total_net',
        'status',
        'issued_at',
        'paid_at'
    ];

protected $casts = [
    'issued_at' => 'datetime',
    'paid_at' => 'datetime',
];

public function customer()
{
    return $this->belongsTo(Customer::class);
}
}
