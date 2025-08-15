<?php

namespace App\Models;

use App\Models\ecommerce\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $fillable = [
        "id",
        "title",
        "slug",
        "excerpt",
        "in_summary",
        "body",
        "category_id",
        "seo_keywords",
        "seo_title",
        "seo_description",
        "seo_status",
        "is_breaking",
        "is_featured",
        "is_sponsored",
        "is_published",
        "is_public",
        "is_trashed",
        "featured_image",
        "post_label",
        "homepage_ordering",
        "total_views",
        "total_shares",
        "total_likes",
        "total_dislikes",
        "notification_sent",
        "created_at",
        "last_updated_at",
        "published_at",
        "created_by",
        "published_by",
        "current_editor",
        "status_id",
        "display_ads",
        "is_premium"
    ];

    public $timestamps = false;

    // hide body column

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id');
    }

    public function authors()
    {
        return $this->belongsToMany(User::class, 'post_authors', 'post_id', 'author_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, "status_id");
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function main_image()
    {
        return $this->belongsTo(Image::class, "featured_image");
    }

    public function scopeIsBreaking($query)
    {
        return $query->where('is_breaking', '=', "1");
    }

    public function scopeIsPublished($query)
    {
        return $query->where('status_id', '=', "3");
    }

    public function scopeIsScheduled($query)
    {
        return $query->where('status_id', '=', "2");
    }

    public function scopeIsFeatured($query)
    {
        return $query->where('is_featured', '=', "1");
    }

    public function getNextAttribute()
    {
        return static::where('id', '>', $this->id)->orderBy('id', 'asc')->first();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public  function getPreviousAttribute()
    {
        return static::where('id', '<', $this->id)->orderBy('id', 'desc')->first();
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'post_id');
    }
}
