
@extends('layouts.app')

@section('content')
<div class="container mt-4 iso-permission-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Hero Banner --}}
    <div class="iso-hero mb-4">
        <div>
            <div class="iso-kicker">ISO DOCUMENT CONTROL — MANAGEMENT</div>
            <h1>Hak Akses Pembuat Folder & Upload ISO</h1>
            <p>Atur pengguna karyawan yang diberikan izin khusus untuk membuat folder baru dan mengunggah dokumen ISO ke sistem.</p>
        </div>
        <a href="{{ route('iso-documents.index') }}" class="btn btn-light text-dark fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dokumen ISO
        </a>
    </div>

    {{-- Stat Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="iso-stat">
                <span>Total Pengguna Aktif</span>
                <strong>{{ $users->count() }}</strong>
                <small>Pengguna terdaftar di sistem</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="iso-stat iso-stat-teal">
                <span>Akses Otomatis</span>
                <strong>{{ $users->filter(fn($u) => $u->isMaster() || $u->isAdminIt())->count() }}</strong>
                <small>Master & Admin IT (Bawaan)</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="iso-stat iso-stat-gold">
                <span>Akses Khusus Diberikan</span>
                <strong id="allowedCountDisplay">{{ count($allowedIds) }}</strong>
                <small>User Karyawan yang diberi izin</small>
            </div>
        </div>
    </div>

    {{-- Info Callout --}}
    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center gap-3">
        <i class="bi bi-info-circle-fill fs-3 text-info"></i>
        <div>
            <strong>Catatan Hak Akses:</strong>
            Pengguna dengan role <strong>Master</strong> dan <strong>Admin IT</strong> secara otomatis memiliki hak membuat folder dan mengunggah file tanpa perlu dicentang. Centang pengguna ber-role <strong>User / Karyawan</strong> di bawah ini untuk memberi mereka izin pembuatan dokumen ISO.
        </div>
    </div>

    <form method="POST" action="{{ route('iso-documents.allowed-creators.update') }}" id="permissionForm">
        @csrf

        <div class="card iso-panel mb-4">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="iso-kicker">DAFTAR PENGGUNA</div>
                    <h2 class="h5 mb-0">Pilih Pengguna Berizin</h2>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <input type="text" id="userSearchInput" class="form-control form-control-sm search-box" placeholder="Cari nama, email, atau departemen..." style="width: 250px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllUsers(true)">
                        <i class="bi bi-check-all me-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllUsers(false)">
                        <i class="bi bi-x-circle me-1"></i> Hapus Semua
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3" id="userCardList">
                    @foreach($users as $user)
                        @php
                            $isAuto = $user->isMaster() || $user->isAdminIt();
                            $isChecked = in_array($user->id, $allowedIds);
                        @endphp
                        <div class="col-md-6 col-lg-4 user-card-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-dept="{{ strtolower($user->department ?? '') }}">
                            <div class="user-permission-card p-3 rounded border h-100 {{ $isAuto ? 'is-auto-access' : ($isChecked ? 'is-checked' : '') }}" onclick="toggleUserCheckbox(event, 'user_chk_{{ $user->id }}')">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-truncate font-weight-bold" style="max-width: 180px;">{{ $user->name }}</h6>
                                            <small class="text-muted d-block text-truncate" style="max-width: 180px;">{{ $user->email }}</small>
                                        </div>
                                    </div>

                                    <div>
                                        @if($isAuto)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" title="Akses penuh secara default">
                                                <i class="bi bi-shield-check me-1"></i> Otomatis
                                            </span>
                                        @else
                                            <div class="form-check form-switch m-0 fs-5">
                                                <input class="form-check-input user-checkbox" type="checkbox" name="allowed_user_ids[]" value="{{ $user->id }}" id="user_chk_{{ $user->id }}" {{ $isChecked ? 'checked' : '' }} onchange="updateCardState(this)">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between fs-7">
                                    <span class="badge {{ $user->isMaster() ? 'bg-danger' : ($user->isAdminIt() ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                        {{ $user->roleLabel() }}
                                    </span>
                                    <span class="text-muted">
                                        <i class="bi bi-building me-1"></i>{{ $user->department ?: '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="noResults" class="text-center py-5 text-muted d-none">
                    <i class="bi bi-search fs-1 mb-2 d-block"></i>
                    Tidak ada pengguna yang cocok dengan pencarian.
                </div>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-between bg-light py-3">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Perubahan akan langsung berlaku setelah disimpan.
                </span>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Hak Akses
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.iso-permission-page { color: #18324a; }
.iso-hero { display: flex; justify-content: space-between; align-items: center; gap: 24px; padding: 28px 32px; background: linear-gradient(120deg, #075985, #0f766e); color: #fff; border-radius: 8px; }
.iso-kicker { font-size: .7rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.iso-hero h1 { margin: 5px 0; font-size: 1.8rem; }
.iso-hero p { max-width: 620px; margin: 0; color: #dbeafe; }
.iso-stat { padding: 16px 18px; background: #fff; border: 1px solid #dbe5ef; border-top: 4px solid #075985; border-radius: 6px; }
.iso-stat span, .iso-stat small { display: block; color: #64748b; font-size: .78rem; }
.iso-stat strong { display: block; font-size: 1.65rem; color: #0f172a; }
.iso-stat-teal { border-top-color: #0f766e; }
.iso-stat-gold { border-top-color: #ca8a04; }
.iso-panel { border: 1px solid #dbe5ef; border-radius: 8px; }
.iso-panel .card-header { padding: 18px 20px; background: #f8fafc; }
.search-box { border-radius: 6px; }

.user-permission-card {
    background: #fff;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    position: relative;
}
.user-permission-card:hover {
    border-color: #0284c7 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.user-permission-card.is-checked {
    background: #f0f9ff;
    border-color: #0284c7 !important;
}
.user-permission-card.is-auto-access {
    background: #f0fdf4;
    border-color: #bbf7d0 !important;
    cursor: default;
}
.avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.fs-7 { font-size: 0.8rem; }

@media (max-width: 576px) {
    .iso-hero { padding: 22px; align-items: flex-start; flex-direction: column; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearchInput');
    const userCards = document.querySelectorAll('.user-card-item');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        let matchCount = 0;

        userCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const email = card.getAttribute('data-email');
            const dept = card.getAttribute('data-dept');

            if (name.includes(query) || email.includes(query) || dept.includes(query)) {
                card.classList.remove('d-none');
                matchCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        if (matchCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    });
});

function toggleUserCheckbox(event, chkId) {
    // Prevent double toggle if clicking directly on input
    if (event.target.tagName === 'INPUT') return;

    const chk = document.getElementById(chkId);
    if (chk) {
        chk.checked = !chk.checked;
        updateCardState(chk);
    }
}

function updateCardState(chk) {
    const card = chk.closest('.user-permission-card');
    if (chk.checked) {
        card.classList.add('is-checked');
    } else {
        card.classList.remove('is-checked');
    }
    updateAllowedCountDisplay();
}

function selectAllUsers(select) {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(chk => {
        // Only toggle visible cards if search is active
        const cardItem = chk.closest('.user-card-item');
        if (!cardItem.classList.contains('d-none')) {
            chk.checked = select;
            updateCardState(chk);
        }
    });
}

function updateAllowedCountDisplay() {
    const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
    const display = document.getElementById('allowedCountDisplay');
    if (display) {
        display.textContent = checkedCount;
    }
}
</script>
@endsection
