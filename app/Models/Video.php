<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        "title", "source", "video_id", "description", "title", "thumbnail_sm", "thumbnail_md", "thumbnail_lg", "live", "published", "published_at", "updated_by", "updated_at"
    ];

    public $timestamps = false;

    public function scopeIsPublished($query)
    {
        return $query->where('published', '=', "1");
    }
}
