@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surat-keluar') }}">Surat Keluar</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-0">Detail Surat Keluar</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('surat-keluar') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('surat-keluar.print', $surat->id) }}"
                    class="btn btn-outline-primary d-flex align-items-center gap-2" target="_blank">
                    <i class="bi bi-printer"></i> Cetak
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Detail Surat Card -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>Informasi Surat
                        </h5>
                        <span
                            class="badge
                        {{ $surat->status === 'disetujui' ? 'bg-success' : ($surat->status === 'ditolak' ? 'bg-danger' : 'bg-warning') }}
                        d-flex align-items-center gap-1 px-3 py-2">
                            <i
                                class="bi {{ $surat->status === 'disetujui' ? 'bi-check-circle' : ($surat->status === 'ditolak' ? 'bi-x-circle' : 'bi-hourglass-split') }}"></i>
                            {{ ucfirst($surat->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="bi bi-hash text-primary"></i>
                                        <span>Nomor Surat</span>
                                    </div>
                                    <div class="detail-value">{{ $surat->nomor_surat }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="bi bi-calendar-date text-primary"></i>
                                        <span>Tanggal</span>
                                    </div>
                                    <div class="detail-value">{{ \Carbon\Carbon::parse($surat->tanggal)->format('d F Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="bi bi-chat-square-text text-primary"></i>
                                        <span>Perihal</span>
                                    </div>
                                    <div class="detail-value">{{ $surat->perihal }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="bi bi-file-text text-primary"></i>
                                        <span>Isi Surat</span>
                                    </div>
                                    <div class="detail-value content-box">
                                        {!! nl2br(e($surat->isi)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lampiran Section (kalo ada) -->
                @if (isset($lampiran) && $lampiran->count())
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-people me-2"></i>Data Pengaju
                                <span class="badge bg-primary rounded-pill ms-2">{{ $lampiran->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-3">No</th>
                                            <th>Nama</th>
                                            <th>NIS/NPM</th>
                                            <th>No. Polisi</th>
                                            <th>Jenis Kendaraan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lampiran as $i => $item)
                                            @php $data = json_decode($item->isi, true); @endphp
                                            @if ($data)
                                                <tr>
                                                    <td class="px-3">{{ $i + 1 }}</td>
                                                    <td>{{ $data['nama'] ?? '-' }}</td>
                                                    <td>{{ $data['npm'] ?? '-' }}</td>
                                                    <td>{{ $data['nopol'] ?? '-' }}</td>
                                                    <td>{{ $data['jenis_kendaraan'] ?? '-' }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="px-3">{{ $i + 1 }}</td>
                                                    <td colspan="4">{{ $item->isi }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-info-circle me-2"></i>Status Surat
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Timeline -->
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Dibuat</h6>
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($surat->created_at)->format('d M Y, H:i') }}</small>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div
                                    class="timeline-marker {{ $surat->status !== 'menunggu' ? 'bg-success' : 'bg-secondary' }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Diajukan</h6>
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($surat->updated_at)->format('d M Y, H:i') }}</small>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div
                                    class="timeline-marker
                                {{ $surat->status === 'disetujui'
                                    ? 'bg-success'
                                    : ($surat->status === 'ditolak'
                                        ? 'bg-danger'
                                        : 'bg-secondary') }}">
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">
                                        {{ $surat->status === 'disetujui'
                                            ? 'Disetujui'
                                            : ($surat->status === 'ditolak'
                                                ? 'Ditolak'
                                                : 'Menunggu Persetujuan') }}
                                    </h6>
                                    @if ($surat->status !== 'menunggu')
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($surat->updated_at)->format('d M Y, H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-gear me-2"></i>Tindakan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($surat->status === 'menunggu')
                                <a href="{{ route('surat-keluar.review', $surat->id) }}"
                                    class="btn btn-warning d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-pencil-square"></i> Review Surat
                                </a>
                            @endif

                            @if ($surat->status === 'disetujui')
                                <a href="{{ route('surat-keluar.print', $surat->id) }}"
                                    class="btn btn-success d-flex align-items-center justify-content-center gap-2"
                                    target="_blank">
                                    <i class="bi bi-printer"></i> Cetak Surat
                                </a>
                            @endif

                            @if ($surat->status === 'ditolak')
                                <a href="{{ route('surat-revisi.edit', $surat->id) }}"
                                    class="btn btn-secondary d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-pencil"></i> Revisi Surat
                                </a>
                            @endif

                            <form action="{{ route('surat-keluar.destroy', $surat->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 w-100"
                                    onclick="return confirm('Yakin ingin menghapus surat ini?')">
                                    <i class="bi bi-trash"></i> Hapus Surat
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Metadata Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-card-list me-2"></i>Metadata
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Dibuat pada</span>
                                <span
                                    class="text-muted">{{ \Carbon\Carbon::parse($surat->created_at)->format('d M Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Terakhir diubah</span>
                                <span
                                    class="text-muted">{{ \Carbon\Carbon::parse($surat->updated_at)->format('d M Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>ID Surat</span>
                                <span class="text-muted">{{ $surat->id }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Detail Item */
        .detail-item {
            margin-bottom: 0.5rem;
        }

        .detail-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .detail-value {
            font-weight: 500;
            font-size: 1rem;
        }

        .content-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem;
            white-space: pre-line;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 9px;
            width: 2px;
            background-color: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content {
            padding-bottom: 10px;
        }

        .timeline-item:last-child .timeline-content {
            padding-bottom: 0;
        }
    </style>
@endsection
