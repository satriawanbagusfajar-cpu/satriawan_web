<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@pkl.test',
        ], [
            'name' => 'Admin PKL',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $siswaUser = User::updateOrCreate([
            'email' => 'siswa@pkl.test',
        ], [
            'name' => 'Siswa PKL',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        Siswa::updateOrCreate([
            'user_id' => $siswaUser->id,
        ], [
            'nama' => 'Siswa PKL',
            'nis' => 'NIS001',
            'kelas' => 'XII RPL 1',
            'jurusan' => 'RPL',
            'perusahaan_id' => null,
        ]);

        // Call seeders lainnya
        $this->call([
            BagusSeeder::class,
            SitiSeeder::class,
            PembimbingSeeder::class,
        ]);
    }
}
