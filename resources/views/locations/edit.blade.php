@extends('layouts.app')

@section('title', 'Edit Lokasi Stok')
@section('page-header', 'Edit Master Lokasi Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h4 class="card-title mb-0 fw-bold">Edit Lokasi: {{ $location->name }}</h4>
                <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('locations.update', $location->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-bold text-muted text-uppercase">Nama Lokasi / Gudang / Toko <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $location->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label small fw-bold text-muted text-uppercase">Tipe Lokasi <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required onchange="toggleResellerUserSelect(this.value)">
                            <option value="warehouse" {{ old('type', $location->type) === 'warehouse' ? 'selected' : '' }}>Gudang / Owner (Pusat)</option>
                            <option value="reseller" {{ old('type', $location->type) === 'reseller' ? 'selected' : '' }}>Reseller Mitra</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 {{ old('type', $location->type) === 'reseller' ? '' : 'd-none' }}" id="resellerUserWrapper">
                        <label for="user_id" class="form-label small fw-bold text-muted text-uppercase">Akun Reseller Terkait <span class="text-muted fw-normal">(Opsional)</span></label>
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

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('locations.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
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
