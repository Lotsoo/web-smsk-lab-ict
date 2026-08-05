@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Surat Keluar</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-0">Surat Keluar</h2>
                <p class="text-muted">Kelola dan pantau semua surat keluar</p>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="btnTambahSurat">
                <i class="bi bi-plus-circle"></i> Tambah Surat Keluar
            </button>
        </div>

        <!-- Status Alert -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>{{ session('status') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form Tambah Surat Keluar (Hidden by default) -->
        <div class="card shadow-sm border-0 mb-4" id="formTambahSurat" style="display: none;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Surat Keluar
                </h5>
                <button type="button" class="btn-close" id="btnCloseForm" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <form action="{{ route('surat-keluar.store') }}" method="POST" id="suratKeluarForm" class="row g-3">
                    @csrf
                    <!-- Step 1: Pilih Jenis Surat -->
                    <div class="col-md-12 mb-3">
                        <label for="jenis_surat" class="form-label fw-medium">Jenis Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-envelope-paper"></i>
                            </span>
                            <select id="jenis_surat" name="jenis_surat" class="form-select" required>
                                <option value="">-- Pilih Jenis Surat --</option>
                                <option value="SA">Sertif Asisten / PKL</option>
                                <option value="SS">Sertif Webinar / Workshop / Media Partner</option>
                                <option value="H">Surat Perbaikan</option>
                                <option value="P">Formulir Pendaftaran Calas</option>
                                <option value="S">SK Asisten / Keterangan</option>
                                <option value="U">Surat Undangan</option>
                                <option value="K">Surat Keputusan</option>
                                <option value="PP">Pengajuan Parkir PKL</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Form Fields Container -->
                    <div id="dynamicFormFields" class="col-12"></div>

                    <!-- Form Buttons -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2">
                            <button type="submit" name="aksi" value="simpan_draft" class="btn btn-secondary">
                                <i class="bi bi-save me-1"></i>Simpan Draft
                            </button>
                            <button type="button" id="btnPreview" class="btn btn-info text-white">
                                <i class="bi bi-eye me-1"></i>Preview
                            </button>
                            <button type="submit" name="aksi" value="ajukan" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>Ajukan Persetujuan
                            </button>
                            <button type="reset" id="btnReset" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Surat Keluar -->
        <div class="card shadow-sm border-0 mb-5 overflow-hidden">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-envelope-paper me-2"></i>Daftar Surat Keluar
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
                    <table class="table table-hover align-middle mb-0" id="suratTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Jenis Surat</th>
                                <th class="py-3">No. Surat</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Isi</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataSurat as $surat)
                                @php
                                    $kodeSurat = [
                                        'SA' => 'Sertif Asisten / PKL',
                                        'SS' => 'Sertif Webinar / Workshop / Media Partner',
                                        'H' => 'Surat Perbaikan',
                                        'P' => 'Formulir Pendaftaran Calas',
                                        'S' => 'SK Asisten / Keterangan',
                                        'U' => 'Surat Undangan',
                                        'K' => 'Surat Keputusan',
                                        'PP' => 'Pengajuan Parkir PKL',
                                    ];

                                    $jenis_surat = $kodeSurat[$surat->jenis] ?? $surat->jenis;

                                    $statusClass = match ($surat->status) {
                                        'menunggu' => 'warning',
                                        'sudah_mengajukan' => 'warning',
                                        'ditolak' => 'danger',
                                        'disetujui' => 'success',
                                        'draft' => 'secondary',
                                        default => 'secondary',
                                    };

                                    $statusIcon = match ($surat->status) {
                                        'menunggu' => 'bi-hourglass-split',
                                        'sudah_mengajukan' => 'bi-send',
                                        'ditolak' => 'bi-x-circle',
                                        'disetujui' => 'bi-check-circle',
                                        'draft' => 'bi-save',
                                        default => 'bi-question-circle',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4">
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                            {{ $jenis_surat }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $surat->nomor_surat }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($surat->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="badge bg-{{ $statusClass }} d-flex align-items-center gap-1 px-2 py-1">
                                                <i class="bi {{ $statusIcon }}"></i>
                                                {{ $surat->status === 'sudah_mengajukan' ? 'Sudah Mengajukan' : ucfirst($surat->status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                            {{ $surat->isi }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('surat-keluar.show', $surat->id) }}"
                                                class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                                title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($surat->status === 'draft')
                                                <a href="{{ route('surat-keluar.edit', $surat->id) }}"
                                                    class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                    title="Edit Draft">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif

                                            <form action="{{ route('surat-keluar.destroy', $surat->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus?')"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            @if ($surat->status === 'ditolak')
                                                <a href="{{ route('surat-revisi.edit', $surat->id) }}"
                                                    class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip"
                                                    title="Revisi">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif

                                            @php
                                                $status = [
                                                    'menunggu',
                                                    'diperbaiki',
                                                    // 'dicetak',
                                                    // 'draft',
                                                    'sudah_mengajukan',
                                                ];
                                            @endphp

                                            @if (in_array($surat->status, $status) && auth()->user()->role === 'superadmin')
                                                <a href="{{ route('surat-keluar.review', $surat->id) }}"
                                                    class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                                    title="Review">
                                                    <i class="bi bi-clipboard-check"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-2">
                    <div class="text-muted small">
                        @if ($dataSurat->total() > 0)
                            Menampilkan {{ $dataSurat->firstItem() }} - {{ $dataSurat->lastItem() }} dari total
                            {{ $dataSurat->total() }} surat
                        @else
                            Tidak ada surat keluar
                        @endif
                    </div>
                    {{ $dataSurat->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">Preview Surat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="previewContent">
                        <!-- Preview content will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="btnSaveAfterPreview">Simpan Draft</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Toggle form visibility
            const btnTambahSurat = document.getElementById('btnTambahSurat');
            const btnCloseForm = document.getElementById('btnCloseForm');
            const formTambahSurat = document.getElementById('formTambahSurat');

            btnTambahSurat.addEventListener('click', function() {
                formTambahSurat.style.display = 'block';
                // Scroll to form
                formTambahSurat.scrollIntoView({
                    behavior: 'smooth'
                });
            });

            btnCloseForm.addEventListener('click', function() {
                formTambahSurat.style.display = 'none';
            });

            // Dynamic form fields based on selected jenis surat
            const jenisSurat = document.getElementById('jenis_surat');
            const dynamicFormFields = document.getElementById('dynamicFormFields');
            const btnReset = document.getElementById('btnReset');

            jenisSurat.addEventListener('change', function() {
                // Reset form fields when jenis surat changes
                dynamicFormFields.innerHTML = '';

                const selectedJenis = this.value;
                if (!selectedJenis) return;

                // Load appropriate form fields based on selected jenis
                switch (selectedJenis) {
                    case 'PP': // Pengajuan Parkir PKL
                        loadPengajuanParkirForm();
                        break;
                    case 'SA': // Sertif Asisten / PKL
                        loadSertifAsistenForm();
                        break;
                    case 'U': // Surat Undangan
                        loadSuratUndanganForm();
                        break;
                    default:
                        loadDefaultForm();
                        break;
                }
            });

            // Reset button handler
            btnReset.addEventListener('click', function() {
                // Reset jenis_surat to trigger change event
                jenisSurat.value = '';
                dynamicFormFields.innerHTML = '';
            });

            // Search functionality
            document.getElementById('searchInput').addEventListener('keyup', function() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById('searchInput');
                filter = input.value.toUpperCase();
                table = document.getElementById('suratTable');
                tr = table.getElementsByTagName('tr');

                for (i = 0; i < tr.length; i++) {
                    // Skip header row
                    if (i === 0) continue;

                    let visible = false;
                    // Check all columns
                    for (let j = 0; j < 5; j++) {
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

            // Preview button handler
            const btnPreview = document.getElementById('btnPreview');
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            const previewContent = document.getElementById('previewContent');

            btnPreview.addEventListener('click', function() {
                // Get form data
                const formData = new FormData(document.getElementById('suratKeluarForm'));

                // Generate preview based on jenis surat
                const selectedJenis = jenisSurat.value;
                if (!selectedJenis) {
                    alert('Silakan pilih jenis surat terlebih dahulu');
                    return;
                }

                // Generate preview content
                let previewHtml = generatePreview(selectedJenis, formData);
                previewContent.innerHTML = previewHtml;

                // Show preview modal
                previewModal.show();
            });

            // Save draft button handler - removed since we're using direct form submission now
            // const btnSimpanDraft = document.getElementById('btnSimpanDraft');
            //
            // btnSimpanDraft.addEventListener('click', function() {
            //     // Add hidden field for draft status
            //     const draftInput = document.createElement('input');
            //     draftInput.type = 'hidden';
            //     draftInput.name = 'aksi';
            //     draftInput.value = 'simpan_draft';
            //     document.getElementById('suratKeluarForm').appendChild(draftInput);
            //
            //     // Submit form
            //     document.getElementById('suratKeluarForm').submit();
            // });

            // Save after preview button handler
            const btnSaveAfterPreview = document.getElementById('btnSaveAfterPreview');

            btnSaveAfterPreview.addEventListener('click', function() {
                // Add hidden field for draft status
                const draftInput = document.createElement('input');
                draftInput.type = 'hidden';
                draftInput.name = 'aksi';
                draftInput.value = 'simpan_draft';
                document.getElementById('suratKeluarForm').appendChild(draftInput);

                // Submit form
                document.getElementById('suratKeluarForm').submit();

                // Hide modal
                bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
            });

            // Function to load Pengajuan Parkir PKL form
            function loadPengajuanParkirForm() {
                // Get next nomor surat
                const nextNomorSurat = "{{ $nextNomorSurat ?? 'XX/XX/XX/XXXX' }}";

                dynamicFormFields.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Nomor Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-hash"></i>
                            </span>
                            <input type="text" name="nomor_surat" class="form-control bg-light" value="${nextNomorSurat}" readonly>
                        </div>
                        <div class="form-text">Nomor surat akan dibuat otomatis</div>
                    </div>

                    <div class="col-md-4">
                        <label for="perihal" class="form-label fw-medium">Perihal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-chat-square-text"></i>
                            </span>
                            <input type="text" name="perihal" id="perihal" class="form-control" value="Permohonan Flat Rate Parkir PKL" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal" class="form-label fw-medium">Tanggal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-date"></i>
                            </span>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="ditujukan_kepada" class="form-label fw-medium">Ditujukan Kepada</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control" value="Kepala Biro Umum" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="jabatan_penerima" class="form-label fw-medium">Jabatan Penerima</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-briefcase"></i>
                            </span>
                            <input type="text" name="jabatan_penerima" id="jabatan_penerima" class="form-control" value="Universitas Budi Luhur" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label for="jumlah_bulan" class="form-label fw-medium">Jumlah Bulan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-month"></i>
                            </span>
                            <input type="number" name="jumlah_bulan" id="jumlah_bulan" class="form-control" min="1" max="12" value="3" required>
                        </div>
                        <div class="form-text">Jumlah bulan pengajuan flat rate parkir</div>
                    </div>

                    <div class="col-12">
                        <label for="isi_surat" class="form-label fw-medium">Isi Surat</label>
                        <textarea name="isi_surat" id="isi_surat" rows="5" class="form-control" readonly>Dengan hormat,

Sehubungan dengan kegiatan Praktik Kerja Lapangan (PKL) Siswa Sekolah Menengah Kejuruan (SMK) di lingkungan Universitas Budi Luhur, dengan ini kami mengajukan permohonan flat rate parkir untuk Siswa tersebut selama periode PKL.

Adapun Siswa yang mengajukan flat rate parkir adalah sebagaimana terlampir.

Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</textarea>
                        <div class="form-text">Isi surat menggunakan template standar</div>
                    </div>

                    <!-- Daftar Pengaju -->
                    <div class="col-12 mt-3">
                        <label class="form-label fw-medium">Daftar Pengaju Flat Rate Parkir</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablePengaju">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Nomor Polisi</th>
                                        <th>Jenis Kendaraan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="pengaju[0][nama]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="pengaju[0][npm]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="pengaju[0][nopol]" class="form-control" required>
                                        </td>
                                        <td>
                                            <select name="pengaju[0][jenis_kendaraan]" class="form-select" required>
                                                <option value="Motor">Motor</option>
                                                <option value="Mobil">Mobil</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <button type="button" class="btn btn-sm btn-success w-100" onclick="tambahPengaju()">
                                                <i class="bi bi-plus-circle me-1"></i>Tambah Pengaju
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            `;


                window.pengajuIndex = 1;
            }


            function loadSertifAsistenForm() {

                const nextNomorSurat = "{{ $nextNomorSurat ?? 'XX/XX/XX/XXXX' }}";

                dynamicFormFields.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Nomor Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-hash"></i>
                            </span>
                            <input type="text" name="nomor_surat" class="form-control bg-light" value="${nextNomorSurat}" readonly>
                        </div>
                        <div class="form-text">Nomor surat akan dibuat otomatis</div>
                    </div>

                    <div class="col-md-4">
                        <label for="perihal" class="form-label fw-medium">Perihal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-chat-square-text"></i>
                            </span>
                            <input type="text" name="perihal" id="perihal" class="form-control" placeholder="Perihal Surat" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal" class="form-label fw-medium">Tanggal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-date"></i>
                            </span>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_kegiatan" class="form-label fw-medium">Nama Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-activity"></i>
                            </span>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="semester" class="form-label fw-medium">Semester</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar3"></i>
                            </span>
                            <select name="semester" id="semester" class="form-select" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                            <input type="text" name="tahun_ajaran" placeholder="Tahun Ajaran (cth: 2024/2025)" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="isi_surat" class="form-label fw-medium">Isi Surat</label>
                        <textarea name="isi_surat" id="isi_surat" rows="5" class="form-control" required></textarea>
                    </div>

                    <!-- Daftar Asisten -->
                    <div class="col-12 mt-3">
                        <label class="form-label fw-medium">Daftar Penerima Sertifikat</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableAsisten">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NIM/NIS</th>
                                        <th>Mata Kuliah/Kegiatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" name="asisten[0][nama]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="asisten[0][npm]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="asisten[0][matkul]" class="form-control" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <button type="button" class="btn btn-sm btn-success w-100" onclick="tambahAsisten()">
                                                <i class="bi bi-plus-circle me-1"></i>Tambah Asisten
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            `;


                window.asistenIndex = 1;
            }


            function loadSuratUndanganForm() {

                const nextNomorSurat = "{{ $nextNomorSurat ?? 'XX/XX/XX/XXXX' }}";

                dynamicFormFields.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Nomor Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-hash"></i>
                            </span>
                            <input type="text" name="nomor_surat" class="form-control bg-light" value="${nextNomorSurat}" readonly>
                        </div>
                        <div class="form-text">Nomor surat akan dibuat otomatis</div>
                    </div>

                    <div class="col-md-4">
                        <label for="perihal" class="form-label fw-medium">Perihal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-chat-square-text"></i>
                            </span>
                            <input type="text" name="perihal" id="perihal" class="form-control" placeholder="Perihal Surat" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal" class="form-label fw-medium">Tanggal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-date"></i>
                            </span>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="ditujukan_kepada" class="form-label fw-medium">Ditujukan Kepada</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="jabatan_penerima" class="form-label fw-medium">Jabatan Penerima</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-briefcase"></i>
                            </span>
                            <input type="text" name="jabatan_penerima" id="jabatan_penerima" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_kegiatan" class="form-label fw-medium">Nama Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-activity"></i>
                            </span>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="tempat_kegiatan" class="form-label fw-medium">Tempat Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" name="tempat_kegiatan" id="tempat_kegiatan" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_kegiatan" class="form-label fw-medium">Tanggal Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="waktu_kegiatan" class="form-label fw-medium">Waktu Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-clock"></i>
                            </span>
                            <input type="time" name="waktu_mulai" class="form-control" required>
                            <span class="input-group-text">s/d</span>
                            <input type="time" name="waktu_selesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="isi_surat" class="form-label fw-medium">Isi Surat</label>
                        <textarea name="isi_surat" id="isi_surat" rows="5" class="form-control" required></textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="lampiran" class="form-label fw-medium">Lampiran (jumlah halaman)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-paperclip"></i>
                            </span>
                            <input type="number" name="lampiran" id="lampiran" class="form-control" min="0" value="0" required>
                        </div>
                    </div>
                </div>
            `;
            }


            function loadDefaultForm() {

                const nextNomorSurat = "{{ $nextNomorSurat ?? 'XX/XX/XX/XXXX' }}";

                dynamicFormFields.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Nomor Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-hash"></i>
                            </span>
                            <input type="text" name="nomor_surat" class="form-control bg-light" value="${nextNomorSurat}" readonly>
                        </div>
                        <div class="form-text">Nomor surat akan dibuat otomatis</div>
                    </div>

                    <div class="col-md-4">
                        <label for="perihal" class="form-label fw-medium">Perihal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-chat-square-text"></i>
                            </span>
                            <input type="text" name="perihal" id="perihal" class="form-control" placeholder="Perihal Surat" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="lampiran" class="form-label fw-medium">Lampiran (jumlah halaman)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-paperclip"></i>
                            </span>
                            <input type="number" name="lampiran" id="lampiran" class="form-control" min="0" value="0" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal" class="form-label fw-medium">Tanggal Surat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-calendar-date"></i>
                            </span>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="isi_surat" class="form-label fw-medium">Isi Surat</label>
                        <textarea name="isi_surat" id="isi_surat" rows="5" class="form-control" placeholder="Isi surat..." required></textarea>
                    </div>

                    <!-- Lampiran Dinamis -->
                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-medium mb-0">
                                <i class="bi bi-paperclip me-1"></i>Lampiran Tambahan (Opsional)
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="tambahLampiran()">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Baris Lampiran
                            </button>
                        </div>
                        <div id="lampiran-container">
                            <div class="row mb-2 lampiran-group">
                                <div class="col-md-5">
                                    <input type="text" name="lampiran[0][label]" class="form-control" placeholder="Label (misal: Nama)">
                                </div>
                                <div class="col-md-7">
                                    <input type="text" name="lampiran[0][isi]" class="form-control" placeholder="Isi (misal: John Doe)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;


                window.indexLampiran = 1;
            }


            window.tambahPengaju = function() {
                const tablePengaju = document.querySelector('#tablePengaju tbody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td>
                    <input type="text" name="pengaju[${window.pengajuIndex}][nama]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="pengaju[${window.pengajuIndex}][npm]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="pengaju[${window.pengajuIndex}][nopol]" class="form-control" required>
                </td>
                <td>
                    <select name="pengaju[${window.pengajuIndex}][jenis_kendaraan]" class="form-select" required>
                        <option value="Motor">Motor</option>
                        <option value="Mobil">Mobil</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusPengaju(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
                tablePengaju.appendChild(newRow);
                window.pengajuIndex++;
            };


            window.hapusPengaju = function(button) {
                const row = button.closest('tr');
                row.remove();
            };


            window.tambahAsisten = function() {
                const tableAsisten = document.querySelector('#tableAsisten tbody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td>
                    <input type="text" name="asisten[${window.asistenIndex}][nama]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="asisten[${window.asistenIndex}][npm]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="asisten[${window.asistenIndex}][matkul]" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusAsisten(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
                tableAsisten.appendChild(newRow);
                window.asistenIndex++;
            };


            window.hapusAsisten = function(button) {
                const row = button.closest('tr');
                row.remove();
            };


            window.tambahLampiran = function() {
                const container = document.getElementById('lampiran-container');
                const row = document.createElement('div');
                row.className = 'row mb-2 lampiran-group';
                row.innerHTML = `
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="lampiran[${window.indexLampiran}][label]" class="form-control" placeholder="Label">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="lampiran[${window.indexLampiran}][isi]" class="form-control" placeholder="Isi">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusLampiran(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
                container.appendChild(row);
                window.indexLampiran++;
            };


            window.hapusLampiran = function(button) {
                const row = button.closest('.lampiran-group');
                row.remove();
            };


            function generatePreview(jenisSurat, formData) {
                let previewHtml = '';


                const perihal = formData.get('perihal') || '';
                const nomorSurat = formData.get('nomor_surat') || '';
                const tanggal = formData.get('tanggal') ? new Date(formData.get('tanggal')).toLocaleDateString(
                    'id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    }) : '';
                const isiSurat = formData.get('isi_surat') || '';


                previewHtml += `
                <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h4 style="margin-bottom: 5px;">UNIVERSITAS BUDI LURUH</h4>
                        <h3 style="margin-top: 0; margin-bottom: 5px;">FAKULTAS ILMU KOMPUTER</h3>
                        <p style="margin-top: 0; font-size: 14px;">Jl. Ciledug Raya Petukangan Utara Jakarta Selatan</p>
                        <hr style="border-top: 3px solid #000; margin-top: 10px;">
                    </div>
            `;


                previewHtml += `
                <div style="margin-bottom: 20px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 120px;">Nomor</td>
                            <td>: ${nomorSurat}</td>
                            <td style="text-align: right;">Jakarta, ${tanggal}</td>
                        </tr>
                        <tr>
                            <td>Perihal</td>
                            <td colspan="2">: ${perihal}</td>
                        </tr>
                    </table>
                </div>
            `;


                switch (jenisSurat) {
                    case 'PP':
                        const ditujukanKepada = formData.get('ditujukan_kepada') || '';
                        const jabatanPenerima = formData.get('jabatan_penerima') || '';
                        const jumlahBulan = formData.get('jumlah_bulan') || '';


                        previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <p>Kepada Yth.<br>
                            ${ditujukanKepada}<br>
                            ${jabatanPenerima}</p>
                        </div>
                    `;


                        previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;


                        previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <h4>Daftar Pengaju Flat Rate Parkir (${jumlahBulan} bulan):</h4>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NPM</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Nomor Polisi</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Jenis Kendaraan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;


                        let pengajuCount = 0;
                        while (formData.get(`pengaju[${pengajuCount}][nama]`)) {
                            const nama = formData.get(`pengaju[${pengajuCount}][nama]`) || '';
                            const npm = formData.get(`pengaju[${pengajuCount}][npm]`) || '';
                            const nopol = formData.get(`pengaju[${pengajuCount}][nopol]`) || '';
                            const jenisKendaraan = formData.get(`pengaju[${pengajuCount}][jenis_kendaraan]`) || '';

                            previewHtml += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${pengajuCount + 1}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${nama}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${npm}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${nopol}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${jenisKendaraan}</td>
                            </tr>
                        `;

                            pengajuCount++;
                        }

                        previewHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                        break;

                    case 'SA':
                        const namaKegiatan = formData.get('nama_kegiatan') || '';
                        const semester = formData.get('semester') || '';
                        const tahunAjaran = formData.get('tahun_ajaran') || '';


                        previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;


                        previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <h4>Daftar Penerima Sertifikat ${namaKegiatan} Semester ${semester} ${tahunAjaran}:</h4>
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                <thead>
                                    <tr style="background-color: #f2f2f2;">
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">NPM</th>
                                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Mata Kuliah/Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;


                        let asistenCount = 0;
                        while (formData.get(`asisten[${asistenCount}][nama]`)) {
                            const nama = formData.get(`asisten[${asistenCount}][nama]`) || '';
                            const npm = formData.get(`asisten[${asistenCount}][npm]`) || '';
                            const matkul = formData.get(`asisten[${asistenCount}][matkul]`) || '';

                            previewHtml += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${asistenCount + 1}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${nama}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${npm}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${matkul}</td>
                            </tr>
                        `;

                            asistenCount++;
                        }

                        previewHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                        break;

                    case 'U':
                        const ditujukanKepadaU = formData.get('ditujukan_kepada') || '';
                        const jabatanPenerimaU = formData.get('jabatan_penerima') || '';
                        const namaKegiatanU = formData.get('nama_kegiatan') || '';
                        const tempatKegiatan = formData.get('tempat_kegiatan') || '';
                        const tanggalKegiatan = formData.get('tanggal_kegiatan') ? new Date(formData.get(
                            'tanggal_kegiatan')).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        }) : '';
                        const waktuMulai = formData.get('waktu_mulai') || '';
                        const waktuSelesai = formData.get('waktu_selesai') || '';


                        previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <p>Kepada Yth.<br>
                            ${ditujukanKepadaU}<br>
                            ${jabatanPenerimaU}</p>
                        </div>
                    `;


                        previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;


                        previewHtml += `
                        <div style="margin-bottom: 20px;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 120px; vertical-align: top;">Acara</td>
                                    <td style="vertical-align: top;">: ${namaKegiatanU}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Hari/Tanggal</td>
                                    <td style="vertical-align: top;">: ${tanggalKegiatan}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Waktu</td>
                                    <td style="vertical-align: top;">: ${waktuMulai} - ${waktuSelesai} WIB</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">Tempat</td>
                                    <td style="vertical-align: top;">: ${tempatKegiatan}</td>
                                </tr>
                            </table>
                        </div>
                    `;
                        break;

                    default:

                        previewHtml += `
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p>${isiSurat.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;


                        let lampiranCount = 0;
                        while (formData.get(`lampiran[${lampiranCount}][label]`)) {
                            if (lampiranCount === 0) {
                                previewHtml += `
                                <div style="margin-bottom: 20px;">
                                    <h4>Lampiran:</h4>
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                        <thead>
                                            <tr style="background-color: #f2f2f2;">
                                                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">No</th>
                                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Label</th>
                                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Isi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            }

                            const label = formData.get(`lampiran[${lampiranCount}][label]`) || '';
                            const isi = formData.get(`lampiran[${lampiranCount}][isi]`) || '';

                            if (label && isi) {
                                previewHtml += `
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${lampiranCount + 1}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${label}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${isi}</td>
                                </tr>
                            `;
                            }

                            lampiranCount++;
                        }

                        if (lampiranCount > 0) {
                            previewHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                        }
                        break;
                }


                previewHtml += `
                <div style="margin-top: 30px; text-align: right;">
                    <p>Hormat kami,<br><br><br><br>
                    <strong>Nama Penandatangan</strong><br>
                    Jabatan</p>
                </div>
            `;


                previewHtml += `</div>`;

                return previewHtml;
            }
        });
    </script>
@endsection
