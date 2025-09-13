<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Models;
use App\Models\Purchase;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_first_login',
        'profile_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function purchases()
    {
        return $this->hasMany(\App\Models\Purchase::class, 'buyer_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getAvatarUrlAttribute(): string
{
    $path = optional($this->profile)->profile_image;

    if ($path && Storage::disk('public')->exists($path)) {
        return Storage::url($path);
    }
    return 'https://ui-avatars.com/api/?name='
        . urlencode($this->name ?? 'U')
        . '&background=random&color=fff&size=96';
}
}