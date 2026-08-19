@extends('layouts.app')

@section('content')
<div class="container mt-4 user-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="user-eyebrow">Pengaturan</div>
            <h2 class="mb-1">Pengaturan User</h2>
            <p class="text-muted mb-0">Kelola akun, hak akses, dan peralatan IT yang dipegang setiap user.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-brand">Tambah User</a>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><div class="user-stat total"><span>Total User</span><strong>{{ $summary['total'] }}</strong><small>akun terdaftar</small></div></div>
        <div class="col-6 col-lg-3"><div class="user-stat master"><span>Master</span><strong>{{ $summary['master'] }}</strong><small>akses penuh</small></div></div>
        <div class="col-6 col-lg-3"><div class="user-stat admin"><span>Admin IT</span><strong>{{ $summary['admin_it'] }}</strong><small>tanpa jadwal & log</small></div></div>
        <div class="col-6 col-lg-3"><div class="user-stat employee"><span>User</span><strong>{{ $summary['user'] }}</strong><small>karyawan</small></div></div>
    </div>
    <div class="card user-filter mb-3">
        <div class="card-header"><strong>Filter User</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                <div class="col-6 col-lg-3"><label class="form-label">Hak Akses</label><select name="role" class="form-select"><option value="">Semua</option>@foreach(\App\Models\User::ROLE_LABELS as $key => $label)<option value="{{ $key }}" @selected($role === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-brand btn-sm">Terapkan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card user-list">
        <div class="card-header"><strong>Daftar User</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Email</th><th>Departemen</th><th>Hak Akses</th><th>Peralatan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($users as $item)
                            <tr>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->department ?: '-' }}</td>
                                <td><span class="role-badge role-{{ $item->role }}">{{ $item->roleLabel() }}</span></td>
                                <td>{{ $item->equipments_count }} unit</td>
                                <td>{!! $item->is_active ? '<span class="role-badge role-active">Aktif</span>' : '<span class="role-badge role-inactive">Nonaktif</span>' !!}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('users.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('users.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-pagination">{{ $users->links() }}</div>
        </div>
    </div>
</div>
<style>.user-eyebrow{color:#0b5ea8;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em}.user-stat{padding:15px 17px;background:#fff;border:1px solid #dbe5ef;border-top:4px solid #64748b}.user-stat span,.user-stat small{display:block;color:#64748b;font-size:.76rem}.user-stat strong{display:block;font-size:1.65rem}.user-stat.total{border-top-color:#0b5ea8}.user-stat.master{border-top-color:#b91c1c}.user-stat.admin{border-top-color:#f6b322}.user-stat.employee{border-top-color:#159957}.user-list,.user-filter{border:1px solid #dbe5ef}.user-list .card-header,.user-filter .card-header{background:#f8fafc}.user-filter .form-label{font-size:.76rem;font-weight:700;color:#475569;margin-bottom:3px}.role-badge{display:inline-block;padding:4px 7px;border-radius:3px;font-size:.74rem;font-weight:700}.role-master{background:#fee2e2;color:#991b1b}.role-admin_it{background:#fef3c7;color:#92400e}.role-user{background:#dcfce7;color:#166534}.role-active{background:#dbeafe;color:#1d4ed8}.role-inactive{background:#e2e8f0;color:#475569}</style>
@endsection
