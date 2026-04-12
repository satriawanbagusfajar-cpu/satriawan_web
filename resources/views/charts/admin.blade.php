@extends('layouts.app')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-1"><i class="bi bi-bar-chart-fill me-2"></i>Grafik Kehadiran Siswa</h3>
    <p class="text-muted mb-0">Visualisasi data kehadiran seluruh siswa PKL</p>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card card-modern">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-1" style="color:#FF8C42;"></i>Diagram Batang (Total)</h6>
                <canvas id="barChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card card-modern">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-1" style="color:#FF6B35;"></i>Diagram Lingkaran</h6>
                <canvas id="pieChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Monthly Chart --}}
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card card-modern">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-1" style="color:#10b981;"></i>Grafik Absensi Per Bulan ({{ $monthly['tahun'] }})</h6>
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($data['labels']);
    const values = @json($data['values']);
    const colors = ['#10b981', '#f59e0b', '#06b6d4', '#ef4444'];
    const bgAlpha = ['rgba(16,185,129,0.2)', 'rgba(245,158,11,0.2)', 'rgba(6,182,212,0.2)', 'rgba(239,68,68,0.2)'];

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah',
                data: values,
                backgroundColor: bgAlpha,
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Monthly Chart
    const monthlyLabels = @json($monthly['labels']);
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    label: 'Hadir',
                    data: @json($monthly['hadir']),
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Izin',
                    data: @json($monthly['izin']),
                    backgroundColor: 'rgba(245,158,11,0.7)',
                    borderColor: '#f59e0b',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Sakit',
                    data: @json($monthly['sakit']),
                    backgroundColor: 'rgba(6,182,212,0.7)',
                    borderColor: '#06b6d4',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Alpha',
                    data: @json($monthly['alpha']),
                    backgroundColor: 'rgba(239,68,68,0.7)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: { stacked: false },
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endpush
