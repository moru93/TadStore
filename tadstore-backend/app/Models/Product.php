<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Product extends Model
{
    use HasFactory;
    protected $fillable = [ 'category_id', 'name', 'description', 'price', 'stock', 'image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    protected $appends = ['image_full_url'];

    public function getImageFullUrlAttribute()
    {
        if (!$this->image_url) {
            return null;
        }

        // Si ya es una URL absoluta, no la toques
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        // Si está en storage
        if (Storage::disk('public')->exists($this->image_url)) {
            return asset('storage/' . $this->image_url);
        }

        // Si está en public directamente
        return asset($this->image_url);
    }
}
