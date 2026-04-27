<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table): void {
            $table->string('lokasi')->nullable()->after('foto');
            $table->decimal('latitude', 10, 7)->nullable()->after('lokasi');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('longitude');
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
        });

        DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'terlambat', 'alpha') NOT NULL DEFAULT 'hadir'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha') NOT NULL DEFAULT 'hadir'");

        Schema::table('absensi', function (Blueprint $table): void {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'lokasi',
                'latitude',
                'longitude',
                'approval_status',
                'approved_by',
                'approved_at',
                'approval_notes',
            ]);
        });
    }
};
