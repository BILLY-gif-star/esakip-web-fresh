<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    use HasFactory;

    protected $table = 'pesan';
    
    protected $fillable = [
        'percakapan_id',
        'pengirim_id',
        'isi',
        'dibaca_at'
    ];

    protected $casts = [
        'dibaca_at' => 'datetime',
    ];

    public function percakapan()
    {
        return $this->belongsTo(Percakapan::class, 'percakapan_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function isDibaca()
    {
        return !is_null($this->dibaca_at);
    }

    public function tandaiDibaca()
    {
        if (!$this->isDibaca()) {
            $this->update(['dibaca_at' => now()]);
        }
    }
}