<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lke_penilaian', function (Blueprint $table) {
            $table->string('jawaban_operator', 5)->nullable()->after('jawaban');
            $table->decimal('nilai_operator', 8, 2)->nullable()->after('nilai');
            $table->decimal('persentase_operator', 5, 2)->nullable()->after('persentase');
            $table->unsignedBigInteger('dinilai_operator_oleh')->nullable()->after('dinilai_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('lke_penilaian', function (Blueprint $table) {
            $table->dropColumn(['jawaban_operator', 'nilai_operator', 'persentase_operator', 'dinilai_operator_oleh']);
        });
    }
};