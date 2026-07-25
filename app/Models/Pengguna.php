<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;

    protected $table = 'pengguna';
    
    protected $fillable = [
        'username',
        'password',
        'nama',
        'role',
        'perangkat_daerah_id',
        'is_active',
        'status_daftar',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi untuk chat
    public function percakapanSebagaiOpd()
    {
        return $this->hasMany(Percakapan::class, 'opd_user_id');
    }

    public function percakapanSebagaiAdmin()
    {
        return $this->hasMany(Percakapan::class, 'admin_user_id');
    }

    public function pesanDikirim()
    {
        return $this->hasMany(Pesan::class, 'pengirim_id');
    }
}