<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        "title", "slug", "body", "last_updated_by", "last_updated_at"
    ];

    public $timestamps = false;
}
