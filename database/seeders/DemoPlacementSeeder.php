<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoPlacementSeeder extends Seeder
{
    public function run(): void
    {
        $guruPembimbing = User::where('email', 'guru.pembimbing@pkl.test')->first();

        $placements = [
            [
                'company_name' => 'PT Nusantara Digital Solusi',
                'company_address' => 'Jl. Merdeka No. 10, Jakarta',
                'mentor_name' => 'Rudi Hartono',
                'mentor_email' => 'mentor.nusantara@pkl.test',
                'mentor_password' => 'MentorNusa123',
                'student_name' => 'Andi Pratama',
                'student_nis' => 'NIS1001',
                'student_email' => 'siswa.andi@pkl.test',
                'student_password' => 'SiswaAndi123',
            ],
            [
                'company_name' => 'PT Cipta Karya Mandiri',
                'company_address' => 'Jl. Pahlawan No. 22, Bandung',
                'mentor_name' => 'Sari Wulandari',
                'mentor_email' => 'mentor.cipta@pkl.test',
                'mentor_password' => 'MentorCipta123',
                'student_name' => 'Budi Santoso',
                'student_nis' => 'NIS1002',
                'student_email' => 'siswa.budi@pkl.test',
                'student_password' => 'SiswaBudi123',
            ],
            [
                'company_name' => 'PT Maju Jaya Teknologi',
                'company_address' => 'Jl. Soekarno Hatta No. 8, Surabaya',
                'mentor_name' => 'Dewi Lestari',
                'mentor_email' => 'mentor.majujaya@pkl.test',
                'mentor_password' => 'MentorMaju123',
                'student_name' => 'Citra Anggraini',
                'student_nis' => 'NIS1003',
                'student_email' => 'siswa.citra@pkl.test',
                'student_password' => 'SiswaCitra123',
            ],
            [
                'company_name' => 'PT Global Prima Industri',
                'company_address' => 'Jl. Ahmad Yani No. 55, Semarang',
                'mentor_name' => 'Hendra Gunawan',
                'mentor_email' => 'mentor.globalprima@pkl.test',
                'mentor_password' => 'MentorGlobal123',
                'student_name' => 'Dimas Saputra',
                'student_nis' => 'NIS1004',
                'student_email' => 'siswa.dimas@pkl.test',
                'student_password' => 'SiswaDimas123',
            ],
            [
                'company_name' => 'PT Sentra Inovasi Kreatif',
                'company_address' => 'Jl. Sudirman No. 77, Yogyakarta',
                'mentor_name' => 'Maya Permata',
                'mentor_email' => 'mentor.sentra@pkl.test',
                'mentor_password' => 'MentorSentra123',
                'student_name' => 'Eka Putri',
                'student_nis' => 'NIS1005',
                'student_email' => 'siswa.eka@pkl.test',
                'student_password' => 'SiswaEka123',
            ],
        ];

        foreach ($placements as $placement) {
            $mentor = User::updateOrCreate(
                ['email' => $placement['mentor_email']],
                [
                    'name' => $placement['mentor_name'],
                    'password' => Hash::make($placement['mentor_password']),
                    'role' => 'pembimbing_perusahaan',
                ],
            );

            $company = Perusahaan::updateOrCreate(
                ['nama_perusahaan' => $placement['company_name']],
                [
                    'alamat' => $placement['company_address'],
                    'pembimbing' => $placement['mentor_name'],
                    'pembimbing_id' => $mentor->id,
                ],
            );

            $studentUser = User::updateOrCreate(
                ['email' => $placement['student_email']],
                [
                    'name' => $placement['student_name'],
                    'password' => Hash::make($placement['student_password']),
                    'role' => 'siswa',
                ],
            );

            Siswa::updateOrCreate(
                ['nis' => $placement['student_nis']],
                [
                    'nama' => $placement['student_name'],
                    'kelas' => 'XII RPL 1',
                    'jurusan' => 'RPL',
                    'perusahaan_id' => $company->id,
                    'user_id' => $studentUser->id,
                    'guru_pembimbing_id' => $guruPembimbing?->id,
                ],
            );
        }
    }
}
