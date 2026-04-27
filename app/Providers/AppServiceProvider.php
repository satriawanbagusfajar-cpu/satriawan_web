<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\Siswa;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view): void {
            $pendingAbsensiNavbarCount = 0;
            $pendingJurnalNavbarCount = 0;
            $pendingNavbarCount = 0;

            if (Auth::check() && Auth::user()->role === 'pembimbing_perusahaan') {
                $user = Auth::user();
                $perusahaanIds = $user->perusahaanBimbingan()->pluck('id');
                $siswaIds = Siswa::whereIn('perusahaan_id', $perusahaanIds)->pluck('id');

                $pendingAbsensiNavbarCount = Absensi::whereIn('siswa_id', $siswaIds)
                    ->where('approval_status', 'pending')
                    ->count();

                $pendingJurnalNavbarCount = Jurnal::whereIn('siswa_id', $siswaIds)
                    ->where('approval_status', 'pending')
                    ->count();

                $pendingNavbarCount = $pendingAbsensiNavbarCount + $pendingJurnalNavbarCount;
            }

            $view->with('pendingNavbarCount', $pendingNavbarCount)
                ->with('pendingAbsensiNavbarCount', $pendingAbsensiNavbarCount)
                ->with('pendingJurnalNavbarCount', $pendingJurnalNavbarCount);
        });
    }
}