<?php

namespace Database\Seeders;

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
        User::updateOrCreate([
            'email' => 'admin@pkl.test',
        ], [
            'name' => 'Admin PKL',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::updateOrCreate([
            'email' => 'guru.pembimbing@pkl.test',
        ], [
            'name' => 'Guru Pembimbing Sekolah',
            'password' => Hash::make('gurupkl123'),
            'role' => 'guru_pembimbing',
        ]);

        $this->call([
            DemoPlacementSeeder::class,
        ]);
    }
}
