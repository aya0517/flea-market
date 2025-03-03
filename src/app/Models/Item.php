<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'price', 'condition_id', 'image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'item_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
