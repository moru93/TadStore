<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OrderVerification extends Model
{
    protected $fillable = ['email', 'code', 'expires_at'];

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}
