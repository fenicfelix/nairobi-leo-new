<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\PostAuthor;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $fillable = [
        "id",
        "identifier",
        "thumbnail",
        "first_name",
        "last_name",
        "display_name",
        "biography",
        "group_id",
        "phone_number",
        "email",
        "username",
        "password",
        "email_verified_at",
        "active",
        "user_url",
        "facebook",
        "instagram",
        "linkedin",
        "twitter",
        "added_by",
        "updated_by",
        "remember_token",
        "created_at",
        "updated_at"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_authors', 'author_id', 'post_id');
    }

    public function shows()
    {
        return $this->belongsToMany(Show::class, 'show_hosts', 'host_id', 'show_id');
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, "group_id");
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', '=', "1");
    }

    public function getTotalPostsAttribute()
    {
        return $this->hasMany(PostAuthor::class, 'author_id')->where('author_id', $this->id)->count();
    }
}
