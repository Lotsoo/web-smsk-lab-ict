@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Revisi Surat</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-0">Revisi Surat</h2>
                <p class="text-muted">Kelola dan perbaiki surat yang memerlukan revisi</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('surat-keluar') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Surat Keluar
                </a>
            </div>
        </div>

        <!-- Status Alert -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Daftar Surat untuk Revisi -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i>Surat yang Perlu Direvisi
                    </h5>
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Cari surat..." id="searchInput">
                        <span class="input-group-text bg-primary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="revisiTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Nomor Surat</th>
                                <th class="py-3">Perihal</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Komentar Revisi</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($revisiList as $surat)

                                <tr>
                                    <td class="px-4 fw-medium">{{ $surat->nomor_surat }}</td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                            {{ $surat->perihal ?? 'Tidak ada perihal' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($surat->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        @if ($surat->status == 'ditolak')
                                            <span class="badge bg-danger d-flex align-items-center gap-1 px-2 py-1">
                                                <i class="bi bi-x-circle"></i> Ditolak
                                            </span>
                                        @elseif($surat->status == 'diperbaiki')
                                            <span class="badge bg-warning d-flex align-items-center gap-1 px-2 py-1">
                                                <i class="bi bi-hourglass-split"></i> Menunggu Review
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($surat->komentar_revisi)
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="popover" data-bs-placement="top" title="Komentar Revisi"
                                                data-bs-content="{{ $surat->komentar_revisi }}">
                                                <i class="bi bi-chat-quote"></i> Lihat Komentar
                                            </button>
                                        @else
                                            <span class="text-muted fst-italic">Belum ada komentar</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <!-- Admin Upload Ulang -->
                                        @if (Auth::user()->role == 'admin' && $surat->status == 'ditolak')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#uploadModal{{ $surat->surat_id }}">
                                                <i class="bi bi-upload"></i> Upload Revisi
                                            </button>

                                            <!-- Upload Modal -->
                                            <div class="modal fade" id="uploadModal{{ $surat->surat_id }}" tabindex="-1"
                                                aria-labelledby="uploadModalLabel{{ $surat->surat_id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="uploadModalLabel{{ $surat->surat_id }}">Upload Revisi
                                                                Surat</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('surat-masuk', $surat->surat_id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                @csrf
                                                                <div class="mb-3">
                                                                    <label for="perbaikan_file" class="form-label">File
                                                                        Revisi</label>
                                                                    <input type="file" class="form-control"
                                                                        id="perbaikan_file" name="perbaikan_file"
                                                                        accept=".pdf,.doc,.docx" required>
                                                                    <div class="form-text">Format yang didukung: PDF, DOC,
                                                                        DOCX (Maks. 5MB)</div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="keterangan_revisi"
                                                                        class="form-label">Keterangan Revisi</label>
                                                                    <textarea class="form-control" id="keterangan_revisi" name="keterangan_revisi" rows="3"
                                                                        placeholder="Jelaskan perubahan yang dilakukan..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Upload &
                                                                    Kirim Ulang</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Pak Syarif Lihat Ulang -->
                                        {{-- @if (Auth::user()->name == 'Pak Syarif' && $surat->status == 'diperbaiki') --}}
                                            <a href="{{ route('surat-revisi.edit', $surat->surat_id) }}"
                                                class="btn btn-sm btn-info d-flex align-items-center justify-content-center gap-1">
                                                <i class="bi bi-eye"></i> Review Ulang
                                            </a>
                                        {{-- @endif --}}

                                        <!-- Semua user bisa lihat detail -->
                                        <a href="{{ route('surat-keluar.show', $surat->surat_id) }}"
                                            class="btn btn-sm btn-outline-secondary mt-1">
                                            <i class="bi bi-info-circle"></i> Detail
                                        </a>

                                        <!-- Button Revisi Data - Direct to Form Page -->
                                        {{-- @if (Auth::user()->role == 'admin' && $surat->status == 'ditolak') --}}
                                            <a href="{{ route('surat-revisi.edit', $surat->surat_id) }}"
                                                class="btn btn-sm btn-warning mt-1">
                                                <i class="bi bi-pencil-square"></i> Revisi Data
                                            </a>
                                        {{-- @endif --}}
                                    </td>
                                </tr>
                            @endforeach

                            @if (count($revisiList) == 0)
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">Tidak ada surat yang perlu direvisi saat ini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ count($revisiList) }} surat yang perlu direvisi
                    </div>
                    @if (count($revisiList) > 0)
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    html: true,
                    sanitize: false
                })
            });

            document.getElementById('searchInput').addEventListener('keyup', function() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById('searchInput');
                filter = input.value.toUpperCase();
                table = document.getElementById('revisiTable');
                tr = table.getElementsByTagName('tr');

                for (i = 0; i < tr.length; i++) {
                    // Skip header row
                    if (i === 0) continue;

                    let visible = false;
                    // Check columns 0, 1, 2 (nomor surat, perihal, tanggal)
                    for (let j = 0; j < 3; j++) {
                        td = tr[i].getElementsByTagName('td')[j];
                        if (td) {
                            txtValue = td.textContent || td.innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                visible = true;
                                break;
                            }
                        }
                    }
                    tr[i].style.display = visible ? '' : 'none';
                }
            });
        });
    </script>
@endsection
