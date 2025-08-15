<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPageviews extends Model
{
    use HasFactory;

    public $fillable = [
        "id", "date", "total"
    ];

    public $timestamps = false;

    public function scopeIsTodays($query)
    {
        return $query->where('date', date('Y-m-d'));
    }
}
