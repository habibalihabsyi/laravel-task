<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, HasUuids;
    public function commentable()
    {
        return $this->morphTo();
    }
    protected static function boot()
    {
        parent::boot();

        // Event saat membuat model baru
        static::creating(function ($model) {
            // Mendapatkan ID pengguna yang sedang login
            $user = auth()->user();

            // Menyimpan user_id ke atribut model
            $model->user_id = $user->id;
        });
    }
}
