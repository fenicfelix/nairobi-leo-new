<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPreference extends Model
{
    use HasFactory;

    public $fillable = ["title", "slug", "value", "updated_at", "updated_by"];

    public $timestamps = false;
}
