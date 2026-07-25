<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'ikon',
        'warna',
        'url',
        'referensi_id',
        'nama_opd',
        'dibaca_at'
    ];
}