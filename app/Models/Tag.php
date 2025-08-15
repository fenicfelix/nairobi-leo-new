<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        "id", "name", "slug", "seo_keywords", "seo_title", "seo_description", "seo_status"
    ];

    public $timestamps = false;

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags', 'tag_id', 'post_id');
    }
}
