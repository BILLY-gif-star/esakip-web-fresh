<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perangkat_daerah', function (Blueprint $table) {
            if (!Schema::hasColumn('perangkat_daerah', 'klaster_updated_at')) {
                $table->timestamp('klaster_updated_at')->nullable()->after('tugas');
            }
            if (!Schema::hasColumn('perangkat_daerah', 'klaster_updated_by')) {
                $table->bigInteger('klaster_updated_by')->nullable()->after('klaster_updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('perangkat_daerah', function (Blueprint $table) {
            $table->dropColumn(['klaster_updated_at', 'klaster_updated_by']);
        });
    }
};