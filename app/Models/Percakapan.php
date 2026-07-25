<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Percakapan extends Model
{
    use HasFactory;

    protected $table = 'percakapan';
    
    protected $fillable = [
        'opd_user_id',
        'admin_user_id',
        'is_operator_chat',
        'pesan_terakhir_at'
    ];

    protected $casts = [
        'pesan_terakhir_at' => 'datetime',
        'is_operator_chat' => 'boolean',
    ];

    public function opdUser()
    {
        return $this->belongsTo(User::class, 'opd_user_id');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function pesan()
    {
        return $this->hasMany(Pesan::class, 'percakapan_id');
    }

    public function pesanTerakhir()
    {
        return $this->hasOne(Pesan::class, 'percakapan_id')->latest();
    }
}