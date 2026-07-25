<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Tentukan tabel yang digunakan
     */
    protected $table = 'pengguna';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'nama',
        'email',
        'password',
        'role',
        'perangkat_daerah_id',
        'is_active',
        'status_daftar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ════════════════════════════════════════════════════
    //  RELASI UNTUK FITUR CHAT
    // ════════════════════════════════════════════════════

    /**
     * Relasi ke percakapan sebagai OPD (Operator)
     */
    public function percakapanSebagaiOpd()
    {
        return $this->hasMany(Percakapan::class, 'opd_user_id');
    }

    /**
     * Relasi ke percakapan sebagai Admin
     */
    public function percakapanSebagaiAdmin()
    {
        return $this->hasMany(Percakapan::class, 'admin_user_id');
    }

    /**
     * Relasi ke pesan yang dikirim
     */
    public function pesanDikirim()
    {
        return $this->hasMany(Pesan::class, 'pengirim_id');
    }
}