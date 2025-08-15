<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'title', 'display_title', 'slug', 'menu_id', 'type', 'reference_id', 'url', 'order'];

    public $timestamps = false;

    public function menu()
    {
        return $this->belongsTo(Menu::class, "menu_id");
    }
}
