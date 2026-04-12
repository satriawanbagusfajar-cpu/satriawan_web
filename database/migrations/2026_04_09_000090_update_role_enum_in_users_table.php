<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'siswa', 'guru_pembimbing', 'pembimbing_perusahaan') DEFAULT 'siswa'");
        } else {
            // For other databases, use the standard approach
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'siswa', 'guru_pembimbing', 'pembimbing_perusahaan'])->default('siswa')->change();
            });
        }
    }

    public function down(): void
    {
        // Keep the new enum values to avoid data truncation on rollback
        // In a rollback scenario, we preserve the role values rather than truncating them
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'siswa', 'guru_pembimbing', 'pembimbing_perusahaan') DEFAULT 'siswa'");
        }
    }
};
