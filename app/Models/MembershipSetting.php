<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipSetting extends Model
{
    protected $fillable = ['window_days', 'grace_days'];
}
