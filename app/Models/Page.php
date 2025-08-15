<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        "title", "slug", "body", "template", "seo_keywords", "seo_title", "seo_description", "seo_status", "category_id", "created_by", "updated_by"
    ];
}
