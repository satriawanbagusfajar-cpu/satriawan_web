<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PembimbingSeeder extends Seeder
{
    public function run(): void
    {
        // ============ GURU PEMBIMBING (SEKOLAH) ============
        
        // Guru Pembimbing 1
        $guruPembimbing1 = User::updateOrCreate(
            ['email' => 'guru.pembimbing1@pkl.test'],
            [
                'name' => 'Bu Siti (Guru Pembimbing)',
                'password' => Hash::make('guru123'),
                'role' => 'guru_pembimbing',
            ]
        );

        // Guru Pembimbing 2
        $guruPembimbing2 = User::updateOrCreate(
            ['email' => 'guru.pembimbing2@pkl.test'],
            [
                'name' => 'Pak Ahmad (Guru Pembimbing)',
                'password' => Hash::make('guru456'),
                'role' => 'guru_pembimbing',
            ]
        );

        // ============ PEMBIMBING PERUSAHAAN ============
        
        // Get atau create perusahaan
        $sakha = Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Sakha Internasional'],
            ['alamat' => 'Jakarta', 'pembimbing' => 'PT Sakha']
        );

        $samick = Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Samick'],
            ['alamat' => 'Bandung', 'pembimbing' => 'PT Samick']
        );

        $indofood = Perusahaan::updateOrCreate(
            ['nama_perusahaan' => 'Indofood'],
            ['alamat' => 'Jakarta', 'pembimbing' => 'PT Indofood']
        );

        // Pembimbing Perusahaan 1 - Sakha Internasional
        $pembimbingPt1 = User::updateOrCreate(
            ['email' => 'pembimbing.sakha@pkl.test'],
            [
                'name' => 'Ir. Bambang Sakha',
                'password' => Hash::make('sakha789'),
                'role' => 'pembimbing_perusahaan',
            ]
        );
        $sakha->update(['pembimbing_id' => $pembimbingPt1->id]);

        // Pembimbing Perusahaan 2 - Samick
        $pembimbingPt2 = User::updateOrCreate(
            ['email' => 'pembimbing.samick@pkl.test'],
            [
                'name' => 'Hendra Samick',
                'password' => Hash::make('samick789'),
                'role' => 'pembimbing_perusahaan',
            ]
        );
        $samick->update(['pembimbing_id' => $pembimbingPt2->id]);

        // Pembimbing Perusahaan 3 - Indofood
        $pembimbingPt3 = User::updateOrCreate(
            ['email' => 'pembimbing.indofood@pkl.test'],
            [
                'name' => 'Dewi Indofood',
                'password' => Hash::make('indofood789'),
                'role' => 'pembimbing_perusahaan',
            ]
        );
        $indofood->update(['pembimbing_id' => $pembimbingPt3->id]);

        // ============ ASSIGN GURU PEMBIMBING KE SISWA ============
        Siswa::whereIn('nis', ['NIS002', 'NIS003'])
            ->update(['guru_pembimbing_id' => $guruPembimbing1->id]);

        Siswa::where('nis', 'NIS001')
            ->update(['guru_pembimbing_id' => $guruPembimbing2->id]);
    }
}
