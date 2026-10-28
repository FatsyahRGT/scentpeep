@extends('layouts.app')

@section('title', 'Edit Lokasi Stok')
@section('page-header', 'Edit Master Lokasi Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <h4 class="font-serif card-title mb-0 fw-bold fs-5">Edit Lokasi: {{ $location->name }}</h4>
                <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('locations.update', $location->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Nama Lokasi / Gudang / Toko <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $location->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Tipe Lokasi <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required onchange="toggleResellerUserSelect(this.value)">
                            <option value="warehouse" {{ old('type', $location->type) === 'warehouse' ? 'selected' : '' }}>Gudang / Owner (Pusat)</option>
                            <option value="reseller" {{ old('type', $location->type) === 'reseller' ? 'selected' : '' }}>Reseller Mitra</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 {{ old('type', $location->type) === 'reseller' ? '' : 'd-none' }}" id="resellerUserWrapper">
                        <label for="user_id" class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em;">Akun Reseller Terkait <span class="text-muted fw-normal">(Opsional)</span></label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">-- Pilih Akun User Reseller --</option>
                            @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}" {{ old('user_id', $location->user_id) == $reseller->id ? 'selected' : '' }}>
                                    {{ $reseller->name }} ({{ $reseller->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.75rem;">Reseller yang login akan otomatis dibatasi hanya dapat melihat & menjual stok dari lokasi ini.</small>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
                        <a href="{{ route('locations.index') }}" class="btn btn-light border px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-scent px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Perbarui Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleResellerUserSelect(val) {
        const wrapper = document.getElementById('resellerUserWrapper');
        if (val === 'reseller') {
            wrapper.classList.remove('d-none');
        } else {
            wrapper.classList.add('d-none');
        }
    }
</script>
@endpush
