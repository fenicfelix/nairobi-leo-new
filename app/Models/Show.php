<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    public $fillable = ["title", "slug", "description", "hosts", "synopsis", "seo_keywords", "seo_title", "seo_description", "seo_status", "active", "banner_img", "mobile_img", "created_by", "last_updated_by"];

    public function banner()
    {
        return $this->belongsTo(Image::class, "banner_img");
    }

    public function mobile_banner()
    {
        return $this->belongsTo(Image::class, "mobile_img");
    }

    public function hosts()
    {
        return $this->belongsToMany(User::class, 'show_hosts', 'show_id', 'host_id');
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', '=', "1");
    }
}
