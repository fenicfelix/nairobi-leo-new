<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramLineup extends Model
{
    use HasFactory;

    public $fillable = ["show_id", "day", "start_time", "end_time", "created_by", "last_updated_by"];

    public function scopeIsTodaysLineup($query)
    {
        return $query->where('day', '=', date('w'));
    }

    public function scopeIsOnAir($query)
    {
        $now = date('H:i:s');
        return $query->isTodaysLineup()->whereRaw(' start_time <= "'.$now.'" and end_time >= "'.$now.'"');
    }

    public function show()
    {
        return $this->belongsTo(Show::class, "show_id");
    }
}
