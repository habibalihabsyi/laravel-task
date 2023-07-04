<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'status',
        'priority',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event saat membuat model baru
        static::creating(function ($model) {
            // Mendapatkan ID pengguna yang sedang login
            $user = auth()->user();

            // Menyimpan user_id ke atribut model
            $model->creator_id = $user->id;
        });
    }
}
