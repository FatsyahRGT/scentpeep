@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-header', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Daftar Pengguna Sistem</h3>
        <p class="text-muted mb-0 small">Kelola akun pengguna, penugasan role (Admin / Staff), dan kontrol status aktif.</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
        </a>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveUserSearch" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Full Akses)</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff (Kasir & Transaksi)</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Filter</button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table mb-0" id="usersTable">
            <thead class="table-light small">
                <tr>
                    <th style="width: 40px;" class="text-muted">#</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th class="text-center" style="width: 120px;">Role</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                    <th class="text-end" style="width: 60px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @forelse($users as $index => $user)
                    <tr>
                        <td class="text-muted small">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-blue-lt fw-bold rounded-circle">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    @if($user->location)
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $user->location->name }}
                                        </div>
                                    @endif
                                    @if($user->id === Auth::id())
                                        <span class="badge bg-light text-primary border" style="font-size: 0.65rem;">Akun Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td class="text-center">
                            @if($user->role === 'admin')
                                <span class="badge bg-purple-lt fw-bold px-2 py-1">ADMIN</span>
                            @elseif($user->role === 'reseller')
                                <span class="badge bg-teal-lt fw-bold px-2 py-1">RESELLER</span>
                            @else
                                <span class="badge bg-cyan-lt fw-bold px-2 py-1">STAFF</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->status === 'pending')
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock me-1"></i>Pending</span>
                            @elseif($user->status === 'rejected')
                                <span class="badge bg-danger text-white px-2 py-1">Ditolak</span>
                            @elseif($user->is_active)
                                <span class="badge bg-success text-white px-2 py-1">Aktif</span>
                            @else
                                <span class="badge bg-secondary text-white px-2 py-1">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" title="Menu Aksi">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow border-0">
                                    @if($user->status === 'pending')
                                    <form action="{{ route('users.approve', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-success fw-semibold">
                                            <i class="bi bi-check-circle me-2"></i> Setujui Reseller
                                        </button>
                                    </form>
                                    <form action="{{ route('users.reject', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-x-circle me-2"></i> Tolak Pendaftaran
                                        </button>
                                    </form>
                                    <div class="dropdown-divider"></div>
                                    @endif

                                    <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Data
                                    </a>
                                    @if($user->id !== Auth::id())
                                    <form action="{{ route('users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-power text-warning me-2"></i> {{ $user->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirmAction(this, 'Hapus Pengguna', 'Apakah Anda yakin ingin menghapus akun {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Hapus Pengguna
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="no-search-match">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            Belum ada pengguna terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between p-3 border-top">
            <span class="text-muted small">Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
            <div>{{ $users->links('pagination::bootstrap-5') }}</div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setupLiveTableSearch('liveUserSearch', 'usersTableBody');
    });
</script>
@endpush
