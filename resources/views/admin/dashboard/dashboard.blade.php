@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
    <p class="text-muted">Welcome to your dashboard overview.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stats-card">
            <div class="d-flex justify-content-between">
                <p class="mb-0 fw-medium">Total Surat Masuk</p>
            </div>
            <div class="mt-3">
                <p class="h2 fw-bold mb-0">{{ $totalSuratMasuk }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stats-card">
            <div class="d-flex justify-content-between">
                <p class="mb-0 fw-medium">Total Surat Keluar</p>
            </div>
            <div class="mt-3">
                <p class="h2 fw-bold mb-0">{{ $totalSuratKeluar }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stats-card">
            <div class="d-flex justify-content-between">
                <p class="mb-0 fw-medium">Revisi Pending</p>
            </div>
            <div class="mt-3">
                <p class="h2 fw-bold mb-0">{{ $revisiPending }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stats-card">
            <div class="d-flex justify-content-between">
                <p class="mb-0 fw-medium">Berhasil Di Setujui</p>
            </div>
            <div class="mt-3">
                <p class="h2 fw-bold mb-0">{{ $berhasilDisetujui }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title">Recent Activity</h3>
        <div class="mt-4">
            @forelse($recentActivities as $activity)
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="bi {{ $activity->icon ?? 'bi-activity' }}"></i>
                    </div>
                    <div class="activity-content">
                        <p class="mb-0 fw-medium">{{ $activity->title }}</p>
                        <p class="mb-0 small text-muted">{{ $activity->description ?? '-' }}</p>
                        @if($activity->user)
                            <p class="mb-0 small text-muted">By: {{ $activity->user->name ?? 'System' }}</p>
                        @endif
                    </div>
                    <div class="activity-time">{{ $activity->time_ago }}</div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                    <p class="mb-0">Belum ada aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
