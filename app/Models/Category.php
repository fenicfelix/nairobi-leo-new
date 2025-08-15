<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        "id", "name", "slug", "parent", "active", "default", "seo_keywords", "seo_title", "seo_description", "seo_status", "added_by", "updated_by"
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    public function scopeIsDefault($query)
    {
        return $query->where('default', '=', "1");
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', '=', "1");
    }
}
