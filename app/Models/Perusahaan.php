<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'pembimbing',
        'pembimbing_id',
    ];

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function pembimbingPerusahaan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }
}
