@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Kelola User</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
        </ol>
    </nav>
    <h2 class="fw-bold text-primary mb-0">Edit User</h2>
    <p class="text-muted">Perbarui informasi dan perizinan akun pengguna</p>
</div>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit User: {{ $user->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
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
                                value="{{ old('username', $user->username) }}" placeholder="Masukkan username unik" required>
                        </div>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password (Hanya untuk akun sendiri) -->
                    @if($user->id === auth()->id())
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                    placeholder="Biarkan kosong jika tidak ingin mengubah password">
                            </div>
                            <div class="form-text">Kosongkan kolom ini apabila tidak ingin mengganti password Anda.</div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="text" class="form-control bg-light text-muted" value="••••••••" readonly disabled>
                            </div>
                            <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Password hanya dapat diubah oleh pemilik akun sendiri.</div>
                        </div>
                    @endif

                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Role Access <span class="text-danger">*</span></label>
                        @if(auth()->user()->role === 'superadmin')
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-shield-check text-muted"></i></span>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                </select>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        @else
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" class="form-control bg-light" value="{{ $user->role === 'superadmin' ? 'Superadmin' : 'Admin' }}" readonly disabled>
                            </div>
                        @endif
                    </div>

                    <!-- Status Akun -->
                    <div class="mb-4">
                        <label for="is_active" class="form-label fw-medium">Status Akun <span class="text-danger">*</span></label>
                        @if($user->role === 'superadmin')
                            <input type="hidden" name="is_active" value="1">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-check-circle-fill text-success"></i></span>
                                <input type="text" class="form-control bg-light" value="Aktif (Dapat Login)" readonly disabled>
                            </div>
                            <div class="form-text text-warning"><i class="bi bi-info-circle me-1"></i>Akun Superadmin tidak dapat dinonaktifkan.</div>
                        @elseif($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="1">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-check-circle-fill text-success"></i></span>
                                <input type="text" class="form-control bg-light" value="Aktif (Dapat Login)" readonly disabled>
                            </div>
                            <div class="form-text text-warning"><i class="bi bi-info-circle me-1"></i>Anda tidak dapat menonaktifkan akun sendiri.</div>
                        @else
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-toggle-on text-muted"></i></span>
                                <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                    <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif (Deactivated / Dilarang Login)</option>
                                </select>
                            </div>
                            @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
