<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'total_amount',
        'status',
        'confirmation_code',
        'confirmation_status',
        'confirmed_at',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
