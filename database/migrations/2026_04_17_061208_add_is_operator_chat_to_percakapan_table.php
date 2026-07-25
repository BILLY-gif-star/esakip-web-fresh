<?php
// database/migrations/2025_04_17_000002_add_is_operator_chat_to_percakapan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('percakapan', function (Blueprint $table) {
            if (!Schema::hasColumn('percakapan', 'is_operator_chat')) {
                $table->boolean('is_operator_chat')->default(false)->after('admin_user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('percakapan', function (Blueprint $table) {
            if (Schema::hasColumn('percakapan', 'is_operator_chat')) {
                $table->dropColumn('is_operator_chat');
            }
        });
    }
};