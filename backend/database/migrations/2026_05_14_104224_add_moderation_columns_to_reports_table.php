<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('moderated_by')
                ->nullable()
                ->after('moderation_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->after('moderated_by');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn(['moderated_by', 'moderated_at']);
        });
    }
};