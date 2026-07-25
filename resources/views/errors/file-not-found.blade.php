@extends('layouts.app')
@section('title', 'File Tidak Ditemukan')
@section('page-title', 'File Tidak Ditemukan')
@section('page-sub', 'Dokumen tidak tersedia di server')

@section('content')
<div style="text-align:center;padding:60px 20px">
    <div style="font-size:80px;margin-bottom:20px">📄</div>
    <h2 style="color:#1f2937;margin-bottom:12px">File Tidak Ditemukan</h2>
    <p style="color:#6b7280;margin-bottom:24px">File <strong>{{ $file ?? 'dokumen' }}</strong> tidak tersedia di server.<br>Silakan upload ulang dokumen melalui sistem.</p>
    <a href="{{ url()->previous() }}" class="btn-primary-friendly">← Kembali</a>
</div>

<style>
    .btn-primary-friendly {
        background: #4f46e5;
        border: none;
        border-radius: 40px;
        padding: 10px 24px;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary-friendly:hover {
        background: #4338ca;
        text-decoration: none;
        color: white;
    }
</style>
@endsection