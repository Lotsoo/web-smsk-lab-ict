@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Kelola User</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah User</li>
        </ol>
    </nav>
    <h2 class="fw-bold text-primary mb-0">Tambah User Baru</h2>
    <p class="text-muted">Buat pengguna baru untuk akses sistem internal</p>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-person-plus text-primary me-2"></i>Form Pengguna Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-medium">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-at text-muted"></i></span>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
                                value="{{ old('username') }}" placeholder="Masukkan username unik" required>
                        </div>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                placeholder="Minimal 6 karakter" required>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Role Access <span class="text-danger">*</span></label>
                        @if(auth()->user()->role === 'superadmin')
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-shield-check text-muted"></i></span>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="admin" {{ old('role', 'admin') == 'admin' ? 'selected' : '' }}>Admin (Akses Standar)</option>
                                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Superadmin (Akses Penuh / Review)</option>
                                </select>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        @else
                            <input type="hidden" name="role" value="admin">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" class="form-control bg-light" value="Admin (Akses Standar)" readonly disabled>
                            </div>
                            <div class="form-text text-muted">Role pengguna baru secara otomatis diset sebagai Admin.</div>
                        @endif
                    </div>

                    <!-- Status Akun -->
                    <div class="mb-4">
                        <label for="is_active" class="form-label fw-medium">Status Akun <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-toggle-on text-muted"></i></span>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif (Tidak Dapat Login / Deactivated)</option>
                            </select>
                        </div>
                        @error('is_active')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
