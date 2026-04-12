<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function isTerlambat(): bool
    {
        if ($this->status !== 'hadir' || ! $this->jam_masuk) {
            return false;
        }

        return Carbon::parse($this->jam_masuk)->gt(Carbon::createFromFormat('H:i:s', self::JAM_MASUK_MULAI));
    }

    public function getKeteranganWaktuAttribute(): string
    {
        if ($this->status !== 'hadir') {
            return ucfirst($this->status);
        }

        return $this->isTerlambat() ? 'Telat' : 'Tepat Waktu';
    }

    public function getBadgeWaktuAttribute(): string
    {
        if ($this->status === 'hadir') {
            return $this->isTerlambat() ? 'danger' : 'success';
        }

        return match ($this->status) {
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
            default => 'secondary',
        };
    }
}
