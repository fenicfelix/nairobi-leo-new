<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public $fillable = [
        "id", "file_name", "title", "alt_text", "caption", "description", "uploaded_at", "uploaded_by", "updated_at", "updated_by"
    ];

    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
