<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SitiSeeder extends Seeder
{
    public function run(): void
    {
        $samick = Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Samick'],
            ['alamat' => '-', 'pembimbing' => '-']
        );

        // Get guru pembimbing (Bu Siti)
        $guruPembimbing = User::where('email', 'guru.pembimbing1@pkl.test')->first();

        $user = User::updateOrCreate(
            ['email' => 'siti@pkl.test'],
            [
                'name' => 'Siti',
                'password' => Hash::make('siti1234'),
                'role' => 'siswa',
            ]
        );

        Siswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => 'Siti',
                'nis' => 'NIS003',
                'kelas' => 'XII RPL 1',
                'jurusan' => 'RPL',
                'perusahaan_id' => $samick->id,
                'guru_pembimbing_id' => $guruPembimbing?->id,
            ]
        );
    }
}
