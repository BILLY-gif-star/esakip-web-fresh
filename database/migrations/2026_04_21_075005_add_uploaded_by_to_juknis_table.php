<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('juknis', function (Blueprint $table) {
            $table->bigInteger('uploaded_by')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('juknis', function (Blueprint $table) {
            $table->dropColumn('uploaded_by');
        });
    }
};