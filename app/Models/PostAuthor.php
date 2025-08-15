<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAuthor extends Model
{
    use HasFactory;

    public $fillable = ["post_id", "author_id"];

    public $timestamps = false;
}
