<?php
// app/Models/Juknis.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juknis extends Model
{
    protected $table = 'juknis';
    
    protected $fillable = [
        'judul',
        'deskripsi',
        'file_path',
        'file_name',
        'file_size',
        'urutan',
        'is_active',
        'uploaded_by'
    ];
    
    public function uploader()
    {
        return $this->belongsTo(Pengguna::class, 'uploaded_by');
    }
    
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}