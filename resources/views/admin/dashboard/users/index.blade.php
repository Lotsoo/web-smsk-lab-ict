@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kelola User</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-primary mb-0">Kelola User</h2>
        <p class="text-muted mb-0">Manajemen pengguna sistem dan perizinan akses akun</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
            <i class="bi bi-person-plus-fill"></i> Tambah User Baru
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('users.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label fw-medium text-muted small">Cari User</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="Cari berdasarkan nama atau username..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label fw-medium text-muted small">Role</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Semua Role</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label fw-medium text-muted small">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 60px;">No</th>
                        <th>User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="ps-4 fw-medium text-muted">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium text-secondary">
                                {{ $user->username }}
                            </td>
                            <td>
                                @if($user->role === 'superadmin')
                                    <span class="badge bg-purple text-white px-3 py-2 rounded-pill" style="background-color: #6f42c1;">
                                        <i class="bi bi-shield-check me-1"></i> Superadmin
                                    </span>
                                @else
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                                        <i class="bi bi-person-badge me-1"></i> Admin
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group gap-1" role="group">
                                    <!-- Edit Button (Modal Trigger) -->
                                    @if(auth()->user()->role !== 'superadmin' && $user->role === 'superadmin')
                                        <button class="btn btn-sm btn-light border text-muted" disabled title="Admin tidak dapat mengedit akun Superadmin">
                                            <i class="bi bi-lock-fill"></i> Edit
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}" title="Edit User">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    @endif

                                    <!-- Deactivate / Activate Button -->
                                    @if($user->role === 'superadmin')
                                        <button class="btn btn-sm btn-light border text-muted" disabled title="Akun Superadmin tidak dapat dinonaktifkan">
                                            <i class="bi bi-shield-lock"></i> Superadmin
                                        </button>
                                    @elseif($user->id === auth()->id())
                                        <button class="btn btn-sm btn-light border text-muted" disabled title="Akun Sendiri">
                                            <i class="bi bi-shield-lock"></i> Utama
                                        </button>
                                    @else
                                        <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    onclick="return confirm('Apakah Anda yakin ingin MENONAKTIFKAN user {{ $user->name }}? User ini tidak akan dapat login lagi.')"
                                                    title="Deactivate User">
                                                    <i class="bi bi-person-x"></i> Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('Apakah Anda yakin ingin MENGAKTIFKAN user {{ $user->name }}?')"
                                                    title="Activate User">
                                                    <i class="bi bi-person-check"></i> Aktifkan
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Delete Button (Hanya Superadmin) -->
                                        @if(auth()->user()->role === 'superadmin')
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')"
                                                    title="Hapus User">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit User -->
                        @if(auth()->user()->role === 'superadmin' || $user->role !== 'superadmin')
                        <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" aria-labelledby="modalEditUserLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark" id="modalEditUserLabel{{ $user->id }}">Edit User</h5>
                                                <p class="text-muted small mb-0">{{ $user->name }} ({{ $user->username }})</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                                        <div class="modal-body py-4">
                                            <!-- Nama Lengkap -->
                                            <div class="mb-3">
                                                <label for="edit_name_{{ $user->id }}" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name_{{ $user->id }}" name="name"
                                                        value="{{ old('user_id') == $user->id ? old('name') : $user->name }}" placeholder="Masukkan nama lengkap" required>
                                                </div>
                                                @error('name')
                                                    @if(old('user_id') == $user->id)
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @endif
                                                @enderror
                                            </div>

                                            <!-- Username -->
                                            <div class="mb-3">
                                                <label for="edit_username_{{ $user->id }}" class="form-label fw-medium">Username <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i class="bi bi-at text-muted"></i></span>
                                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="edit_username_{{ $user->id }}" name="username"
                                                        value="{{ old('user_id') == $user->id ? old('username') : $user->username }}" placeholder="Masukkan username unik" required>
                                                </div>
                                                @error('username')
                                                    @if(old('user_id') == $user->id)
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @endif
                                                @enderror
                                            </div>

                                            <!-- Password -->
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Password Baru</label>
                                                @if($user->id === auth()->id())
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="edit_password_{{ $user->id }}" name="password"
                                                            placeholder="Biarkan kosong jika tidak diubah">
                                                    </div>
                                                    <div class="form-text">Kosongkan jika tidak ingin mengubah password Anda.</div>
                                                    @error('password')
                                                        @if(old('user_id') == $user->id)
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @endif
                                                    @enderror
                                                @else
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                                                        <input type="text" class="form-control bg-light text-muted" value="••••••••" readonly disabled>
                                                    </div>
                                                    <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Password hanya dapat diubah oleh pemilik akun sendiri.</div>
                                                @endif
                                            </div>

                                            <!-- Role -->
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Role Access <span class="text-danger">*</span></label>
                                                @if(auth()->user()->role === 'superadmin')
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="bi bi-shield-check text-muted"></i></span>
                                                        <select class="form-select @error('role') is-invalid @enderror" id="edit_role_{{ $user->id }}" name="role" required>
                                                            <option value="admin" {{ (old('user_id') == $user->id ? old('role') : $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                                            <option value="superadmin" {{ (old('user_id') == $user->id ? old('role') : $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                                        </select>
                                                    </div>
                                                    @error('role')
                                                        @if(old('user_id') == $user->id)
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @endif
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
                                            <div class="mb-2">
                                                <label class="form-label fw-medium">Status Akun <span class="text-danger">*</span></label>
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
                                                        <select class="form-select @error('is_active') is-invalid @enderror" id="edit_is_active_{{ $user->id }}" name="is_active" required>
                                                            <option value="1" {{ (old('user_id') == $user->id ? old('is_active') : ($user->is_active ? '1' : '0')) == '1' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                                            <option value="0" {{ (old('user_id') == $user->id ? old('is_active') : ($user->is_active ? '1' : '0')) == '0' ? 'selected' : '' }}>Nonaktif (Deactivated / Dilarang Login)</option>
                                                        </select>
                                                    </div>
                                                    @error('is_active')
                                                        @if(old('user_id') == $user->id)
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @endif
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top-0 rounded-bottom">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-lg me-1"></i> Batal
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people d-block fs-1 mb-2"></i>
                                <p class="mb-0 fw-medium">Tidak ada data user yang ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah User Baru -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-labelledby="modalCreateUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-plus fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalCreateUserLabel">Tambah User Baru</h5>
                        <p class="text-muted small mb-0">Buat akun pengguna baru untuk akses sistem</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="create_name" class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name"
                                value="{{ !old('user_id') ? old('name') : '' }}" placeholder="Masukkan nama lengkap" required>
                        </div>
                        @error('name')
                            @if(!old('user_id'))
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="create_username" class="form-label fw-medium">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-at text-muted"></i></span>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="create_username" name="username"
                                value="{{ !old('user_id') ? old('username') : '' }}" placeholder="Masukkan username unik" required>
                        </div>
                        @error('username')
                            @if(!old('user_id'))
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="create_password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="create_password" name="password"
                                placeholder="Minimal 6 karakter" required>
                        </div>
                        @error('password')
                            @if(!old('user_id'))
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <!-- Role Access -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Role Access <span class="text-danger">*</span></label>
                        @if(auth()->user()->role === 'superadmin')
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-shield-check text-muted"></i></span>
                                <select class="form-select @error('role') is-invalid @enderror" id="create_role" name="role" required>
                                    <option value="admin" {{ (!old('user_id') && old('role', 'admin') == 'admin') ? 'selected' : '' }}>Admin</option>
                                    <option value="superadmin" {{ (!old('user_id') && old('role') == 'superadmin') ? 'selected' : '' }}>Superadmin</option>
                                </select>
                            </div>
                            @error('role')
                                @if(!old('user_id'))
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @endif
                            @enderror
                        @else
                            <input type="hidden" name="role" value="admin">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" class="form-control bg-light" value="Admin" readonly disabled>
                            </div>
                            <div class="form-text text-muted">Role pengguna baru secara otomatis diset sebagai Admin.</div>
                        @endif
                    </div>

                    <!-- Status Akun -->
                    <div class="mb-2">
                        <label for="create_is_active" class="form-label fw-medium">Status Akun <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-toggle-on text-muted"></i></span>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="create_is_active" name="is_active" required>
                                <option value="1" {{ (!old('user_id') && old('is_active', '1') == '1') ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                                <option value="0" {{ (!old('user_id') && old('is_active') === '0') ? 'selected' : '' }}>Nonaktif (Tidak Dapat Login)</option>
                            </select>
                        </div>
                        @error('is_active')
                            @if(!old('user_id'))
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('user_id'))
            var editModalEl = document.getElementById('modalEditUser{{ old("user_id") }}');
            if (editModalEl) {
                var editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            }
        @else
            var createModalEl = document.getElementById('modalCreateUser');
            if (createModalEl) {
                var createModal = new bootstrap.Modal(createModalEl);
                createModal.show();
            }
        @endif
    });
</script>
@endif

@endsection
