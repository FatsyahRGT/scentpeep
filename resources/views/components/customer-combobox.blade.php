{{--
    Custom Combobox Pemilihan Nama Customer (Inline CRUD + Custom Input + Auto-Sort A-Z)
    Luxury Monochrome Scentpeep Design System (Bootstrap 5.3 + Vanilla JS Fetch AJAX)
--}}
@php
    $isRequired = $required ?? true;
    $fieldLabel = $label ?? 'Nama Customer / Pembeli';
    $placeholderText = $placeholder ?? 'Ketik nama pembeli atau pilih pelanggan tetap...';
@endphp

<div class="position-relative" id="customerCombobox">
    <label for="customerInput" class="form-label small fw-semibold text-muted d-flex justify-content-between align-items-center mb-1">
        <span>{{ $fieldLabel }} @if($isRequired)<span class="text-danger">*</span>@endif</span>
        <span class="text-muted fw-normal" style="font-size: 0.75rem;">Bisa ketik custom / pilih tetap</span>
    </label>

    <!-- Input Group Utama -->
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0 rounded-start-3">
            <i class="bi bi-person text-muted"></i>
        </span>

        <input type="text"
               id="customerInput"
               name="customer_name"
               class="form-control border-start-0 py-2 @error('customer_name') is-invalid @enderror"
               placeholder="{{ $placeholderText }}"
               autocomplete="off"
               value="{{ old('customer_name', $value ?? '') }}"
               {{ $isRequired ? 'required' : '' }}>

        <input type="hidden" id="customerIdInput" name="customer_id" value="{{ old('customer_id', $customerId ?? '') }}">

        <!-- Tombol + Jadikan Tetap (Muncul otomatis saat mengetik nama yang belum terdaftar) -->
        <button type="button"
                id="btnSaveRegular"
                class="btn btn-outline-dark btn-sm px-2 px-md-3 d-none align-items-center gap-1 text-nowrap fw-medium"
                title="Simpan nama ini ke daftar Pelanggan Tetap">
            <i class="bi bi-bookmark-plus-fill"></i>
            <span class="d-none d-sm-inline">+ Jadikan Tetap</span>
            <span class="d-inline d-sm-none">+ Tetap</span>
        </button>

        <!-- Tombol Buka/Tutup Dropdown -->
        <button type="button"
                id="btnToggleDropdown"
                class="btn btn-dark rounded-end-3 px-3"
                title="Lihat Daftar Pelanggan Tetap"
                aria-label="Buka daftar pelanggan tetap">
            <i class="bi bi-chevron-down" id="dropdownIcon"></i>
        </button>
    </div>

    @error('customer_name')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <!-- Baris Tombol Kapsul Rekomendasi Cepat Berurut A-Z (Muncul saat mengetik huruf) -->
    <div id="quickSuggestContainer" class="mt-2 d-none align-items-center flex-wrap gap-1">
        <span class="text-muted me-1 font-mono" style="font-size: 0.72rem;">
            <i class="bi bi-lightning-charge-fill text-warning"></i> Rekomendasi A-Z:
        </span>
        <div id="quickSuggestButtons" class="d-inline-flex flex-wrap gap-1"></div>
    </div>

    <!-- Baris Indikator Status & Feedback -->
    <div class="mt-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-2 fw-medium d-none" id="badgeCustom" style="font-size: 0.75rem;">
                <i class="bi bi-person-dash me-1"></i>Mode: Customer Custom (Sekali Beli)
            </span>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-2 fw-medium d-none" id="badgeRegular" style="font-size: 0.75rem;">
                <i class="bi bi-patch-check-fill me-1"></i>Pelanggan Tetap Terdaftar
            </span>
        </div>
        <span id="toastFeedback" class="small text-success fw-medium d-none"></span>
    </div>

    <!-- Floating Dropdown Menu Pelanggan Tetap (Pilih, Edit, Hapus) -->
    <div id="customerDropdownMenu"
         class="card rounded-3 position-absolute w-100 mt-1 d-none customer-dropdown shadow-lg bg-white"
         style="z-index: 1055; max-height: 280px; overflow-y: auto; border: 1px solid #e5e7eb;">
        
        <div class="px-3 py-2 bg-light border-bottom d-flex justify-content-between align-items-center sticky-top" style="z-index: 2;">
            <span class="text-muted fw-bold font-mono" style="font-size: 0.70rem; letter-spacing: 0.04em;">
                DAFTAR PELANGGAN TETAP (AUTO-SORT A-Z)
            </span>
            <span class="badge bg-white text-dark border font-mono" id="customerCountBadge" style="font-size: 0.70rem;">0 pelanggan</span>
        </div>

        <ul class="list-group list-group-flush" id="customerListContainer"></ul>
    </div>
</div>

<style>
    .customer-dropdown {
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }
    .customer-item {
        transition: background-color 0.15s ease;
    }
    .customer-item:hover {
        background-color: #f9fafb;
    }
    .quick-pill {
        transition: all 0.15s ease;
        font-size: 0.76rem;
    }
    .quick-pill:hover {
        background-color: #111827;
        color: #ffffff;
    }
    .quick-pill:hover strong {
        color: #fbbf24 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Inisialisasi data master pelanggan tetap dari Controller dengan fallback fetch AJAX
    let regularCustomers = @json($customers ?? []);
    if (!regularCustomers || regularCustomers.length === 0) {
        fetch('{{ route('customers.json') }}', { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    regularCustomers = data;
                    checkInputStatus();
                    if (!menu.classList.contains('d-none')) {
                        renderList(input.value);
                    }
                }
            })
            .catch(() => {});
    }

    // Elemen DOM Combobox
    const combobox = document.getElementById('customerCombobox');
    if (!combobox) return;

    const input = document.getElementById('customerInput');
    const idInput = document.getElementById('customerIdInput');
    const btnSave = document.getElementById('btnSaveRegular');
    const btnToggle = document.getElementById('btnToggleDropdown');
    const dropdownIcon = document.getElementById('dropdownIcon');
    const menu = document.getElementById('customerDropdownMenu');
    const listContainer = document.getElementById('customerListContainer');
    const countBadge = document.getElementById('customerCountBadge');
    const badgeCustom = document.getElementById('badgeCustom');
    const badgeRegular = document.getElementById('badgeRegular');
    const quickContainer = document.getElementById('quickSuggestContainer');
    const quickButtons = document.getElementById('quickSuggestButtons');
    const toastFeedback = document.getElementById('toastFeedback');

    let editingId = null;

    // Tampilkan notifikasi singkat
    function showFeedback(message, isError = false) {
        if (!toastFeedback) return;
        toastFeedback.className = isError ? 'small text-danger fw-medium' : 'small text-success fw-medium';
        toastFeedback.innerHTML = `<i class="bi ${isError ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'} me-1"></i>${message}`;
        toastFeedback.classList.remove('d-none');
        setTimeout(() => {
            toastFeedback.classList.add('d-none');
        }, 3200);
    }

    // Algoritma Pengurutan A-Z (Prioritas startsWith -> lalu Alfabetis localeCompare A-Z)
    function getSortedCustomers(keyword = '') {
        const q = keyword.trim().toLowerCase();

        return regularCustomers
            .filter(c => c.name.toLowerCase().includes(q))
            .sort((a, b) => {
                const nameA = a.name.toLowerCase();
                const nameB = b.name.toLowerCase();

                if (q) {
                    const aStarts = nameA.startsWith(q);
                    const bStarts = nameB.startsWith(q);
                    if (aStarts && !bStarts) return -1;
                    if (!aStarts && bStarts) return 1;
                }
                return nameA.localeCompare(nameB, 'id', { sensitivity: 'base' });
            });
    }

    // Highlight huruf yang cocok dengan ketikan pengguna
    function highlightMatch(text, keyword) {
        if (!keyword.trim()) return text;
        const safeKeyword = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp(`(${safeKeyword})`, 'gi'), '<strong class="text-dark text-decoration-underline">$1</strong>');
    }

    // Render baris tombol kapsul rekomendasi cepat (maksimal 6 item)
    function renderQuickButtons(sortedList, keyword) {
        quickButtons.innerHTML = '';

        if (!keyword.trim() || sortedList.length === 0) {
            quickContainer.classList.add('d-none');
            quickContainer.classList.remove('d-flex');
            return;
        }

        quickContainer.classList.remove('d-none');
        quickContainer.classList.add('d-flex');

        sortedList.slice(0, 6).forEach(customer => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-dark btn-sm rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 quick-pill shadow-xs';
            btn.innerHTML = `<i class="bi bi-person-check-fill text-muted"></i> <span>${highlightMatch(customer.name, keyword)}</span>`;

            btn.addEventListener('click', function () {
                input.value = customer.name;
                checkInputStatus();
                closeDropdown();
                quickContainer.classList.add('d-none');
                quickContainer.classList.remove('d-flex');
            });

            quickButtons.appendChild(btn);
        });
    }

    // Render isi dropdown pelanggan tetap
    function renderList(filterText = '') {
        listContainer.innerHTML = '';
        const sorted = getSortedCustomers(filterText);
        if (countBadge) countBadge.textContent = `${sorted.length} pelanggan`;

        renderQuickButtons(sorted, filterText);

        if (sorted.length === 0) {
            if (!filterText.trim()) {
                listContainer.innerHTML = `
                    <li class="list-group-item text-muted small py-4 text-center">
                        <i class="bi bi-people fs-4 d-block mb-1 text-secondary"></i>
                        Belum ada Pelanggan Tetap terdaftar.<br>
                        <span class="text-secondary small">Ketik nama di kolom input lalu klik <strong>"+ Jadikan Tetap"</strong> untuk menambahkan pelanggan pertama Anda.</span>
                    </li>`;
            } else {
                listContainer.innerHTML = `
                    <li class="list-group-item text-muted small py-4 text-center">
                        <i class="bi bi-person-plus fs-4 d-block mb-1 text-secondary"></i>
                        Nama <strong>"${filterText}"</strong> belum ada di daftar Pelanggan Tetap.<br>
                        <span class="text-secondary small">Klik tombol <strong>"+ Jadikan Tetap"</strong> untuk menyimpannya, atau lanjutkan sebagai <em>Customer Custom</em>.</span>
                    </li>`;
            }
            return;
        }

        sorted.forEach(customer => {
            const li = document.createElement('li');
            li.className = 'list-group-item customer-item d-flex justify-content-between align-items-center py-2 px-3';

            if (editingId === customer.id) {
                li.innerHTML = `
                    <div class="input-group input-group-sm me-2">
                        <span class="input-group-text bg-light"><i class="bi bi-pencil-square"></i></span>
                        <input type="text" class="form-control rounded-end-2 font-sans" id="editInput_${customer.id}" value="${customer.name}">
                    </div>
                    <div class="d-inline-flex gap-1 flex-shrink-0">
                        <button type="button" class="btn btn-dark btn-sm rounded-2 px-2 py-1" data-action="save-edit" data-id="${customer.id}" title="Simpan Perubahan">
                            <i class="bi bi-check-lg"></i> <span class="d-none d-sm-inline">Simpan</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-2 py-1" data-action="cancel-edit" title="Batal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
            } else {
                const isSelected = Number(idInput.value) === customer.id;
                li.innerHTML = `
                    <div class="flex-grow-1 text-truncate py-1 d-flex align-items-center gap-2" style="cursor: pointer;" data-action="select" data-id="${customer.id}" data-name="${customer.name}">
                        <i class="bi ${isSelected ? 'bi-check-circle-fill text-success' : 'bi-person-circle text-secondary'}"></i>
                        <span class="${isSelected ? 'fw-bold text-success' : 'fw-medium text-dark'}">${highlightMatch(customer.name, filterText)}</span>
                    </div>
                    <div class="d-inline-flex align-items-center gap-1 flex-shrink-0 ms-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-2 py-1" title="Ubah Nama Pelanggan" data-action="edit" data-id="${customer.id}">
                            <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-2 px-2 py-1" title="Hapus dari Pelanggan Tetap" data-action="delete" data-id="${customer.id}">
                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                `;
            }

            listContainer.appendChild(li);
        });
    }

    // Periksa status kecocokan nama: Pelanggan Tetap vs Custom
    function checkInputStatus() {
        const val = input.value.trim();
        if (!val) {
            btnSave.classList.add('d-none');
            btnSave.classList.remove('d-inline-flex');
            badgeCustom.classList.add('d-none');
            badgeRegular.classList.add('d-none');
            idInput.value = '';
            return;
        }

        const matched = regularCustomers.find(c => c.name.toLowerCase() === val.toLowerCase());
        if (matched) {
            idInput.value = matched.id;
            btnSave.classList.add('d-none');
            btnSave.classList.remove('d-inline-flex');
            badgeRegular.classList.remove('d-none');
            badgeCustom.classList.add('d-none');
        } else {
            idInput.value = '';
            btnSave.classList.remove('d-none');
            btnSave.classList.add('d-inline-flex');
            badgeCustom.classList.remove('d-none');
            badgeRegular.classList.add('d-none');
        }
    }

    function openDropdown() {
        menu.classList.remove('d-none');
        dropdownIcon.className = 'bi bi-chevron-up';
    }

    function closeDropdown() {
        menu.classList.add('d-none');
        dropdownIcon.className = 'bi bi-chevron-down';
        editingId = null;
    }

    // Event input & fokus
    input.addEventListener('input', function () {
        openDropdown();
        checkInputStatus();
        renderList(this.value);
    });

    input.addEventListener('focus', function () {
        openDropdown();
        renderList(this.value);
    });

    // Toggle button dropdown
    btnToggle.addEventListener('click', function () {
        if (menu.classList.contains('d-none')) {
            openDropdown();
            renderList(input.value);
        } else {
            closeDropdown();
        }
    });

    // AJAX 1: POST /customers (Jadikan Pelanggan Tetap)
    btnSave.addEventListener('click', function () {
        const newName = input.value.trim();
        if (!newName) return;

        btnSave.disabled = true;
        const originalHtml = btnSave.innerHTML;
        btnSave.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;

        fetch('{{ route('customers.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name: newName })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            btnSave.disabled = false;
            btnSave.innerHTML = originalHtml;

            if (res.status === 201 || res.body.success) {
                const created = res.body.data;
                regularCustomers.push(created);
                idInput.value = created.id;
                checkInputStatus();
                renderList(newName);
                showFeedback(res.body.message || `"${newName}" disimpan sebagai Pelanggan Tetap!`);
            } else {
                const errMsg = res.body.errors?.name?.[0] || res.body.message || 'Gagal menyimpan pelanggan tetap.';
                showFeedback(errMsg, true);
            }
        })
        .catch(err => {
            btnSave.disabled = false;
            btnSave.innerHTML = originalHtml;
            showFeedback('Terjadi kesalahan jaringan.', true);
        });
    });

    // Delegasi Event di dalam Dropdown (Pilih, Edit, Simpan Edit, Hapus)
    listContainer.addEventListener('click', function (e) {
        const target = e.target.closest('[data-action]');
        if (!target) return;

        const action = target.dataset.action;
        const id = Number(target.dataset.id);

        if (action === 'select') {
            input.value = target.dataset.name;
            checkInputStatus();
            closeDropdown();
            quickContainer.classList.add('d-none');
            quickContainer.classList.remove('d-flex');
        } else if (action === 'edit') {
            editingId = id;
            renderList(input.value);
            const editField = document.getElementById(`editInput_${id}`);
            if (editField) {
                editField.focus();
                editField.select();
                editField.addEventListener('keydown', function (ke) {
                    if (ke.key === 'Enter') {
                        ke.preventDefault();
                        saveInlineEdit(id);
                    } else if (ke.key === 'Escape') {
                        editingId = null;
                        renderList(input.value);
                    }
                });
            }
        } else if (action === 'cancel-edit') {
            editingId = null;
            renderList(input.value);
        } else if (action === 'save-edit') {
            saveInlineEdit(id);
        } else if (action === 'delete') {
            const customerToDelete = regularCustomers.find(c => c.id === id);
            const custName = customerToDelete ? customerToDelete.name : 'pelanggan ini';

            const executeDelete = () => {
                fetch(`/customers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    if (res.body.success) {
                        regularCustomers = regularCustomers.filter(c => c.id !== id);
                        if (Number(idInput.value) === id) {
                            idInput.value = '';
                        }
                        checkInputStatus();
                        renderList(input.value);
                        showFeedback(res.body.message || `Pelanggan "${custName}" berhasil dihapus.`);
                    } else {
                        showFeedback(res.body.message || 'Gagal menghapus pelanggan.', true);
                    }
                })
                .catch(err => {
                    showFeedback('Terjadi kesalahan jaringan.', true);
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Pelanggan Tetap?',
                    text: `Apakah Anda yakin ingin menghapus "${custName}" dari daftar Pelanggan Tetap?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeDelete();
                    }
                });
            } else {
                if (confirm(`Hapus "${custName}" dari daftar Pelanggan Tetap?`)) {
                    executeDelete();
                }
            }
        }
    });

    // AJAX 2: PUT /customers/{id} (Simpan Perubahan Nama)
    function saveInlineEdit(id) {
        const editField = document.getElementById(`editInput_${id}`);
        const updatedName = editField ? editField.value.trim() : '';

        if (!updatedName) {
            showFeedback('Nama pelanggan tidak boleh kosong.', true);
            return;
        }

        fetch(`/customers/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name: updatedName })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.body.success) {
                const idx = regularCustomers.findIndex(c => c.id === id);
                if (idx !== -1) regularCustomers[idx].name = updatedName;
                if (Number(idInput.value) === id) input.value = updatedName;
                showFeedback(res.body.message || `Nama pelanggan diperbarui menjadi "${updatedName}".`);
            } else {
                const errMsg = res.body.errors?.name?.[0] || res.body.message || 'Gagal memperbarui nama pelanggan.';
                showFeedback(errMsg, true);
            }
            editingId = null;
            checkInputStatus();
            renderList(input.value);
        })
        .catch(err => {
            showFeedback('Terjadi kesalahan jaringan.', true);
            editingId = null;
            renderList(input.value);
        });
    }

    // Klik di luar combobox menutup dropdown
    document.addEventListener('click', function (e) {
        if (!combobox.contains(e.target)) {
            closeDropdown();
        }
    });

    // Tombol Escape menutup dropdown
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    // Inisialisasi status awal (berguna saat mode edit form invoice)
    checkInputStatus();
});
</script>
