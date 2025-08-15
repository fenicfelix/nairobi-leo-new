<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowHosts extends Model
{
    use HasFactory;

    public $fillable = [
        "show_id", "host_id"
    ];

    public $timestamps = false;
}
