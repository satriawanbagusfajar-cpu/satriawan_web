<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('guru_pembimbing_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign('siswa_guru_pembimbing_id_foreign');
            // Then drop the column
            $table->dropColumn('guru_pembimbing_id');
        });
    }
};
