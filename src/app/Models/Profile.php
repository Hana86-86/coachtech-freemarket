<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'postal_code', 'address', 'building', 'profile_image'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getImageUrlAttribute(): string
    {
        if($this->profile_image && Storage::disc('public')->exists($this->profile_image)) {
            return Storage::Url($this->profile_image);
        }
        return asset('images/avatar-placeholder.png');
    }

}
