@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Surat Masuk</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-primary mb-0">Surat Masuk</h2>
                <p class="text-muted">Kelola dan pantau semua surat masuk</p>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="btnTambahSurat">
                <i class="bi bi-plus-circle"></i> Tambah Surat Masuk
            </button>
        </div>

        <!-- Status Alert -->
        @if (session('status') || session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>{{ session('status') ?? session('success') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <strong>{{ session('error') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Terdapat kesalahan pada form:</strong>
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Daftar Surat Masuk -->
        <div class="card shadow-sm border-0 mb-5 overflow-hidden">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-envelope-paper me-2"></i>Daftar Surat Masuk
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
                                <th class="py-3">No</th>
                                <th class="py-3">Jenis Surat</th>
                                {{-- <th class="py-3">Nama Surat</th> --}}
                                <th class="py-3">Pengirim</th>
                                <th class="py-3">Instansi</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Perihal</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suratMasuk as $key => $surat)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $surat->jenis_surat }}</td>
                                    {{-- <td>{{ $surat->nama_surat }}</td> --}}
                                    <td>{{ $surat->pengirim }}</td>
                                    <td>{{ $surat->instansi_pengirim }}</td>
                                    <td>{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}</td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                            {{ $surat->perihal }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('surat-masuk.show', $surat->id) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="tooltip"
                                                title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ route('surat-masuk.edit', $surat->id) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               data-bs-toggle="tooltip"
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @if(auth()->user()->role === 'superadmin')
                                            <a href="{{ route('surat-masuk.review', $surat->id) }}"
                                               class="btn btn-sm btn-outline-info"
                                               data-bs-toggle="tooltip"
                                               title="Review">
                                                <i class="bi bi-clipboard-check"></i>
                                            </a>
                                            @endif

                                            <form action="{{ route('surat-masuk.destroy', $surat->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?');"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">Tidak ada data surat masuk</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ count($suratMasuk) }} surat
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div> --}}
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $suratMasuk->count() }} dari {{ $suratMasuk->total() }} surat
                    </div>
                    {{ $suratMasuk->links() }}
                </div>
            </div>
        </div>

        <!-- Form Tambah Surat Masuk -->
        <div class="card shadow-sm border-0 mb-4" id="formTambahSurat" style="display: none;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Surat Masuk
                </h5>
                <button type="button" class="btn-close" id="btnCloseForm" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <form action="{{ route('surat-masuk.store') }}" method="POST" enctype="multipart/form-data"
                    id="formSuratMasuk">
                    @csrf
                    <div class="row">
                        <!-- Preview Column -->
                        <div class="col-lg-5 mb-4 mb-lg-0">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Preview Dokumen</h6>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center p-2">
                                    <div id="documentPreview"
                                        class="text-center mb-3 w-100 d-flex flex-column justify-content-center align-items-center"
                                        style="min-height: 400px;">
                                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 5rem;"></i>
                                        <p class="text-muted mt-3">Unggah dokumen untuk melihat preview</p>
                                    </div>
                                    <div class="w-100 p-3">
                                        <label for="file" class="form-label fw-medium">
                                            <i class="bi bi-upload me-1"></i>Upload Dokumen Surat
                                        </label>
                                        <input type="file" class="form-control" id="file" name="file"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                        <div class="form-text">Format: PDF, DOC(X), JPG, PNG (Maks. 5MB)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields Column -->
                        <div class="col-lg-7">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="jenis_surat" class="form-label fw-medium">Jenis Surat</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-tag"></i>
                                        </span>
                                        <select id="jenis_surat" name="jenis_surat" class="form-select" required>
                                            <option value="">-- Pilih Jenis Surat --</option>
                                            <option value="Peminjaman Lab">Peminjaman Lab</option>
                                            <option value="Informasi Penting">Informasi Penting</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="jenisLainnyaContainer" style="display: none;">
                                    <label for="jenis_lainnya" class="form-label fw-medium">Jenis Surat Lainnya</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-pencil"></i>
                                        </span>
                                        <input type="text" id="jenis_lainnya" name="jenis_lainnya" class="form-control"
                                            placeholder="Masukkan jenis surat">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="tanggal_surat" class="form-label fw-medium">Tanggal Surat</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-calendar-date"></i>
                                        </span>
                                        <input type="date" id="tanggal_surat" name="tanggal_surat" class="form-control"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="perihal" class="form-label fw-medium">Perihal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-chat-square-text"></i>
                                        </span>
                                        <input type="text" id="perihal" name="perihal" class="form-control"
                                            placeholder="Perihal surat" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="pengirim" class="form-label fw-medium">Nama Pengirim</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" id="pengirim" name="pengirim" class="form-control"
                                            placeholder="Nama pengirim surat" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="jabatan_pengirim" class="form-label fw-medium">Jabatan Pengirim</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-briefcase"></i>
                                        </span>
                                        <input type="text" id="jabatan_pengirim" name="jabatan_pengirim"
                                            class="form-control" placeholder="Jabatan pengirim" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="instansi" class="form-label fw-medium">Instansi Pengirim</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <input type="text" id="instansi" name="instansi" class="form-control"
                                            placeholder="Nama instansi" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="diketahui_oleh" class="form-label fw-medium">Diketahui Oleh</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-person-check"></i>
                                        </span>
                                        <input type="text" id="diketahui_oleh" name="diketahui_oleh"
                                            class="form-control" placeholder="Nama yang mengetahui">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="jabatan_diketahui" class="form-label fw-medium">Jabatan yang
                                        Mengetahui</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-briefcase"></i>
                                        </span>
                                        <input type="text" id="jabatan_diketahui" name="jabatan_diketahui"
                                            class="form-control" placeholder="Jabatan yang mengetahui">
                                    </div>
                                </div>

                                <input type="hidden" name="nama_surat" id="nama_surat">

                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="reset" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Toggle form visibility
            const btnTambahSurat = document.getElementById('btnTambahSurat');
            const btnCloseForm = document.getElementById('btnCloseForm');
            const formTambahSurat = document.getElementById('formTambahSurat');
            const daftarSuratMasuk = document.querySelector('.card.shadow-sm.border-0.mb-5.overflow-hidden');

            btnTambahSurat.addEventListener('click', function() {
                formTambahSurat.style.display = 'block';
                daftarSuratMasuk.style.display = 'none';
                // Scroll to form
                formTambahSurat.scrollIntoView({
                    behavior: 'smooth'
                });
            });

            btnCloseForm.addEventListener('click', function() {
                formTambahSurat.style.display = 'none';
                daftarSuratMasuk.style.display = 'block';
                // Scroll back to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Auto-buka form jika ada validation error
            @if ($errors->any())
                formTambahSurat.style.display = 'block';
                daftarSuratMasuk.style.display = 'none';
                formTambahSurat.scrollIntoView({ behavior: 'smooth' });
            @endif

            // Jenis surat lainnya toggle
            const jenisSurat = document.getElementById('jenis_surat');
            const jenisLainnyaContainer = document.getElementById('jenisLainnyaContainer');
            const jenisLainnyaInput = document.getElementById('jenis_lainnya');

            jenisSurat.addEventListener('change', function() {
                if (this.value === 'lainnya') {
                    jenisLainnyaContainer.style.display = 'block';
                    jenisLainnyaInput.setAttribute('required', 'required');
                } else {
                    jenisLainnyaContainer.style.display = 'none';
                    jenisLainnyaInput.removeAttribute('required');
                }
                updateNamaSurat();
            });

            // File preview
            const fileInput = document.getElementById('file');
            const previewContainer = document.getElementById('documentPreview');

            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    previewContainer.innerHTML = '';

                    if (file.type.includes('image')) {
                        // If it's an image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-fluid rounded';
                            img.style.maxHeight = '400px';
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type.includes('pdf')) {
                        // If it's a PDF
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-file-earmark-pdf text-danger';
                        icon.style.fontSize = '5rem';

                        const fileName = document.createElement('p');
                        fileName.className = 'mt-3 fw-medium';
                        fileName.textContent = file.name;

                        previewContainer.appendChild(icon);
                        previewContainer.appendChild(fileName);
                    } else {
                        // For other file types (doc, docx)
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-file-earmark-text text-primary';
                        icon.style.fontSize = '5rem';

                        const fileName = document.createElement('p');
                        fileName.className = 'mt-3 fw-medium';
                        fileName.textContent = file.name;

                        previewContainer.appendChild(icon);
                        previewContainer.appendChild(fileName);
                    }

                    updateNamaSurat();
                }
            });

            // Auto-generate nama surat
            const instansiInput = document.getElementById('instansi');
            const tanggalInput = document.getElementById('tanggal_surat');
            const perihalInput = document.getElementById('perihal');
            const namaSuratInput = document.getElementById('nama_surat');

            function updateNamaSurat() {
                let jenis = jenisSurat.value;
                if (jenis === 'lainnya') {
                    jenis = jenisLainnyaInput.value || 'Surat';
                }

                const instansi = instansiInput.value || 'Umum';
                const tanggal = tanggalInput.value || new Date().toISOString().split('T')[0];
                const perihal = perihalInput.value ? perihalInput.value.substring(0, 20).replace(/\s+/g, '') : '';

                if (jenis && tanggal) {
                    const formattedDate = tanggal.replace(/-/g, '');
                    const cleanInstansi = instansi.replace(/\s+/g, '');
                    namaSuratInput.value = `${jenis}-${cleanInstansi}-${formattedDate}${perihal ? '-' + perihal : ''}`;
                }
            }

            jenisSurat.addEventListener('change', updateNamaSurat);
            jenisLainnyaInput.addEventListener('input', updateNamaSurat);
            instansiInput.addEventListener('input', updateNamaSurat);
            tanggalInput.addEventListener('input', updateNamaSurat);
            perihalInput.addEventListener('input', updateNamaSurat);

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
        });
    </script>
@endsection
