<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Absensi extends Model
{
    use HasFactory;

    private static ?array $tableColumnsCache = null;

    public const JAM_MASUK_MULAI = '07:00:00';

    public const JAM_KELUAR_SELESAI = '16:00:00';

    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'foto',
        'lokasi',
        'latitude',
        'longitude',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function isTerlambat(): bool
    {
        if ($this->status === 'terlambat') {
            return true;
        }

        if ($this->status !== 'hadir' || ! $this->jam_masuk) {
            return false;
        }

        return Carbon::parse($this->jam_masuk)->gt(Carbon::createFromFormat('H:i:s', self::JAM_MASUK_MULAI));
    }

    public function getKeteranganWaktuAttribute(): string
    {
        if ($this->status === 'terlambat') {
            return 'Telat';
        }

        if ($this->status !== 'hadir') {
            return ucfirst($this->status);
        }

        return $this->isTerlambat() ? 'Telat' : 'Tepat Waktu';
    }

    public function getBadgeWaktuAttribute(): string
    {
        if (in_array($this->status, ['hadir', 'terlambat'], true)) {
            return $this->isTerlambat() ? 'danger' : 'success';
        }

        return match ($this->status) {
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
            default => 'secondary',
        };
    }

    public function getApprovalBadgeClassAttribute(): string
    {
        return match ($this->approval_status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
    }

    public static function hasColumn(string $column): bool
    {
        if (self::$tableColumnsCache === null) {
            self::$tableColumnsCache = Schema::getColumnListing((new self())->getTable());
        }

        return in_array($column, self::$tableColumnsCache, true);
    }

    public static function keepExistingColumns(array $payload): array
    {
        return array_filter(
            $payload,
            static fn (mixed $_, string $key): bool => self::hasColumn($key),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    public static function ensureAutoAlphaForToday(): void
    {
        $now = now();
        if ($now->lt($now->copy()->setTime(7, 0, 0))) {
            return;
        }

        $today = $now->toDateString();
        $missingSiswaIds = DB::table('siswa as s')
            ->leftJoin('absensi as a', function ($join) use ($today): void {
                $join->on('a.siswa_id', '=', 's.id')
                    ->whereDate('a.tanggal', '=', $today);
            })
            ->whereNull('a.id')
            ->pluck('s.id');

        if ($missingSiswaIds->isEmpty()) {
            return;
        }

        $rows = $missingSiswaIds->map(function (int $siswaId) use ($today, $now): array {
            return self::keepExistingColumns([
                'siswa_id' => $siswaId,
                'tanggal' => $today,
                'status' => 'alpha',
                'jam_masuk' => null,
                'jam_keluar' => null,
                'foto' => null,
                'lokasi' => null,
                'latitude' => null,
                'longitude' => null,
                'approval_status' => 'approved',
                'approved_by' => null,
                'approved_at' => $now,
                'approval_notes' => 'Status alpha otomatis setelah lewat jam 07:00.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->all();

        DB::table('absensi')->insertOrIgnore($rows);
    }
}
