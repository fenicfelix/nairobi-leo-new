<?php

namespace App\Models\ecommerce;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Post
{
    use HasFactory;

    protected $table = 'ecommerce_products';

    public $fillable = [
        "sku", "other_images", "unit_price", "discounted_price", "unit_measurement", "quantity", "post_id", "other_images"
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
