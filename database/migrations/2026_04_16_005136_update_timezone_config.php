<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        // Update .env file (manual, atau jalankan perintah)
        // Atau buat file untuk mencatat perubahan
        Artisan::call('config:clear');
    }

    public function down(): void
    {
        Artisan::call('config:clear');
    }
};