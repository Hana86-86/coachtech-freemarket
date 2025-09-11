<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        if ($this->profile_image && Storage::disk('public')->exists($this->profile_image)) {
            $ver = $this->updated_at?->timestamp ?? time();
            return Storage::url($this->profile_image) . '?v=' . $ver;
        }

        // 画像未設定時は UI Avatars
        $name = $this->user?->name ?? 'U';
        return 'https://ui-avatars.com/api/?name='
            . urlencode($name)
            . '&background=random&color=fff&size=96';
    }

}
