@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-header', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark">Daftar Pengguna Sistem</h3>
            <small class="text-muted d-block">Kelola akun pengguna, penugasan role (Admin / Staff), dan kontrol status aktif.</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-scent fw-semibold">
                <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
            </a>
            <form method="GET" action="{{ route('users.index') }}" class="d-inline-block">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
            </form>
            <div class="input-group input-group-sm" style="width: 16rem">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="search" id="liveUserSearch" name="search" class="form-control border-start-0 ps-0" placeholder="Filter baris pengguna&hellip;" value="{{ request('search') }}" aria-label="Filter rows">
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0" id="usersTable">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 40px;" class="py-2 px-3 text-muted small fw-semibold">#</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Nama Pengguna</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Email</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Role</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 110px;">Status</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="text-muted small py-2 px-3">{{ $users->firstItem() + $index }}</td>
                            <td class="py-2 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem; background-color: rgba(18,18,18,0.06); color: var(--scent-dark, #121212);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                        @if($user->location)
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $user->location->name }}
                                            </div>
                                        @endif
                                        @if($user->id === Auth::id())
                                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small py-2 px-3">{{ $user->email }}</td>
                            <td class="text-center py-2 px-3">
                                @if($user->role === 'admin')
                                    <span class="badge bg-dark text-white fw-bold px-2 py-1">ADMIN</span>
                                @elseif($user->role === 'reseller')
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">RESELLER</span>
                                @else
                                    <span class="badge bg-secondary text-white fw-bold px-2 py-1">STAFF</span>
                                @endif
                            </td>
                            <td class="text-center py-2 px-3">
                                @if($user->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border px-2 py-1"><i class="bi bi-clock me-1"></i>Pending</span>
                                @elseif($user->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border px-2 py-1">Ditolak</span>
                                @elseif($user->is_active)
                                    <span class="badge bg-success-subtle text-success border px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end py-2 px-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" title="Menu Aksi">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
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
                                            <i class="bi bi-pencil-square text-dark me-2"></i> Edit Data
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
    </div>

    @if($users->hasPages())
        <div class="card-footer clearfix bg-transparent py-3 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
            {{ $users->links('vendor.pagination.bootstrap-5') }}
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
