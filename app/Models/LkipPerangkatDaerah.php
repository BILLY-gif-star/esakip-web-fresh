<?php
// app/Models/LkipPerangkatDaerah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkipPerangkatDaerah extends Model
{
    protected $table = 'lkip_perangkat_daerah';
    
    protected $fillable = [
        'perangkat_daerah_id',
        'tahun',
        'judul_dokumen',
        'deskripsi',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'catatan_review',
        'uploaded_by',
        'reviewed_by',
        'reviewed_at',
        'is_template'
    ];
    
    protected $casts = [
        'tahun' => 'integer',
        'reviewed_at' => 'datetime',
        'is_template' => 'boolean'
    ];
    
    public function perangkatDaerah()
    {
        return $this->belongsTo(PerangkatDaerah::class, 'perangkat_daerah_id');
    }
    
    public function uploader()
    {
        return $this->belongsTo(Pengguna::class, 'uploaded_by');
    }
    
    public function reviewer()
    {
        return $this->belongsTo(Pengguna::class, 'reviewed_by');
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
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
    
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge-status badge-pending">📝 Draft</span>',
            'dikirim' => '<span class="badge-status badge-pending">⏳ Menunggu Review</span>',
            'disetujui' => '<span class="badge-status badge-approved">✅ Disetujui</span>',
            'ditolak' => '<span class="badge-status badge-rejected">❌ Ditolak</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge-status">Unknown</span>';
    }
}