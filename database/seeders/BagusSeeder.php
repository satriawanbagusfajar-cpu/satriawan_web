<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BagusSeeder extends Seeder
{
    public function run(): void
    {
        $p1 = Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Sakha Internasional'],
            ['alamat' => '-', 'pembimbing' => '-']
        );

        Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Samick'],
            ['alamat' => '-', 'pembimbing' => '-']
        );

        Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Indofood'],
            ['alamat' => '-', 'pembimbing' => '-']
        );

        // Get guru pembimbing (Pak Ahmad)
        $guruPembimbing = User::where('email', 'guru.pembimbing2@pkl.test')->first();

        $user = User::updateOrCreate(
            ['email' => 'bagus@pkl.test'],
            [
                'name' => 'Bagus Fajar Satriawan',
                'password' => Hash::make('bagus123'),
                'role' => 'siswa',
            ]
        );

        Siswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama' => 'Bagus Fajar Satriawan',
                'nis' => 'NIS002',
                'kelas' => 'XII RPL 1',
                'jurusan' => 'RPL',
                'perusahaan_id' => $p1->id,
                'guru_pembimbing_id' => $guruPembimbing?->id,
            ]
        );
    }
}
