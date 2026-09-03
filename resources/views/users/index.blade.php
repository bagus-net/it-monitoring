@extends('layouts.app')

@section('content')
<div class="container mt-4 user-page">
    <div class="d-flex justify-content-between align-items-start mb-3"><div class="d-flex align-items-start gap-3"><span class="user-page-icon"><i class="bi bi-people"></i></span><div><div class="user-eyebrow">Pengaturan</div><h2 class="mb-1">Pengaturan User</h2><p class="text-muted mb-0">Kelola akun, hak akses, dan peralatan IT yang dipegang setiap user.</p></div></div><a href="{{ route('users.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg"></i>Tambah User</a></div>
    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3"><div class="user-widget total"><div class="widget-top"><span>Total User</span><i class="bi bi-people-fill widget-icon"></i></div><strong>{{ $summary['total'] }}</strong><small>akun terdaftar</small></div></div>
      <div class="col-6 col-lg-3"><div class="user-widget master"><div class="widget-top"><span>Master</span><i class="bi bi-shield-lock-fill widget-icon"></i></div><strong>{{ $summary['master'] }}</strong><small>akses penuh</small></div></div>
      <div class="col-6 col-lg-3"><div class="user-widget admin"><div class="widget-top"><span>Admin IT</span><i class="bi bi-person-gear widget-icon"></i></div><strong>{{ $summary['admin_it'] }}</strong><small>tanpa jadwal & log</small></div></div>
      <div class="col-6 col-lg-3"><div class="user-widget employee"><div class="widget-top"><span>User</span><i class="bi bi-person-badge widget-icon"></i></div><strong>{{ $summary['user'] }}</strong><small>karyawan</small></div></div>
    </div>
    <div class="card user-filter mb-3">
        <div class="card-header"><strong><i class="bi bi-sliders"></i>Filter User</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                <div class="col-6 col-lg-3"><label class="form-label">Hak Akses</label><select name="role" class="form-select"><option value="">Semua</option>@foreach(\App\Models\User::ROLE_LABELS as $key => $label)<option value="{{ $key }}" @selected($role === $key)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-brand btn-sm"><i class="bi bi-check2"></i>Terapkan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card user-list">
        <div class="card-header"><strong><i class="bi bi-table"></i>Daftar User</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Email</th><th>Departemen</th><th>Hak Akses</th><th>Peralatan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($users as $item)
                            <tr>
                                <td><div class="user-name-cell"><img src="{{ $item->profile_photo_path ? asset('storage/' . $item->profile_photo_path) : asset('images/default-avatar.svg') }}" alt="Foto profil {{ $item->name }}"><strong>{{ $item->name }}</strong></div></td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->department ?: '-' }}</td>
                                <td><span class="role-badge role-{{ $item->role }}">{{ $item->roleLabel() }}</span></td>
                                <td>{{ $item->equipments_count }} unit</td>
                                <td>{!! $item->is_active ? '<span class="role-badge role-active">Aktif</span>' : '<span class="role-badge role-inactive">Nonaktif</span>' !!}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('users.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
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
<style>
.user-page{--ublue:#2161f5;--uteal:#14b8a6}
.user-page-icon{display:flex;align-items:center;justify-content:center;width:46px;height:46px;flex:0 0 46px;border-radius:14px;background:linear-gradient(135deg,var(--ublue),#6ea3ff);color:#fff;font-size:1.15rem;box-shadow:0 8px 16px rgba(33,97,245,.28)}
.user-page .user-eyebrow{color:var(--ublue);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}
.user-page .btn-brand{background:linear-gradient(135deg,var(--ublue),#6ea3ff);box-shadow:0 8px 16px rgba(33,97,245,.24)}
.user-page .btn-brand i,.user-page .card-header strong i{margin-right:7px}
.user-page .card-header strong{display:flex;align-items:center;color:#18243d}
.user-page .card-header strong i{color:var(--ublue)}
.user-page .card{border:1px solid #e7ebf2;border-radius:16px;box-shadow:0 8px 22px rgba(35,52,85,.05)}
.user-page .card-header{padding:16px 20px;border-bottom:1px solid #edf0f5;background:#fff;color:#18243d;font-size:.88rem;font-weight:800;border-radius:16px 16px 0 0}
.user-page .card-body{padding:20px}
.user-page .user-widget{position:relative;min-height:126px;padding:18px;border:1px solid #e7ebf2;border-radius:16px;background:#fff;box-shadow:0 8px 20px rgba(35,52,85,.05)}
.user-page .widget-top{display:flex;align-items:center;justify-content:space-between;color:#8792a7;font-size:.72rem;font-weight:700}
.user-page .widget-icon{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:10px;font-size:.86rem}
.user-page .user-widget strong{display:block;margin:9px 0 3px;color:#18243d;font-size:1.75rem;font-weight:800;letter-spacing:-.02em}
.user-page .user-widget small{display:block;color:#8792a7;font-size:.68rem}
.user-page .user-widget.total .widget-icon{background:#e8f0ff;color:var(--ublue)}
.user-page .user-widget.master .widget-icon{background:#fee2e2;color:#dc2626}
.user-page .user-widget.admin .widget-icon{background:#fef3c7;color:#c2870a}
.user-page .user-widget.employee .widget-icon{background:#e2f8f4;color:var(--uteal)}
.user-page .user-filter .form-label{color:#69758d;font-size:.68rem;font-weight:700;letter-spacing:.02em}
.user-page .form-control,.user-page .form-select{min-height:39px;border:1px solid #dfe5ee;border-radius:9px;background:#f9fafc;color:#34415a;font-size:.76rem}
.user-page .form-control:focus,.user-page .form-select:focus{border-color:#7aa3ff;box-shadow:0 0 0 3px rgba(33,97,245,.1);background:#fff}
.user-page .btn-sm{border-radius:8px;font-size:.7rem;font-weight:700}
.user-page .btn-sm i{margin-right:5px;font-size:.72rem}
.user-page .btn-outline-secondary{border-color:#dfe5ee;color:#68758d}
.user-page .btn-outline-primary{border-color:#b8ccff;color:var(--ublue)}
.user-page .btn-outline-primary:hover{background:var(--ublue);border-color:var(--ublue)}
.user-page .btn-outline-danger{border-color:#f4c2c7;color:#dc5260}
.user-page .user-list .card-body{padding:0}
.user-page .table{font-size:.74rem}
.user-page .table thead th{padding:13px 16px;border-bottom:1px solid #e8edf4;background:#f8fafc;color:#7d899e;font-size:.65rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;white-space:nowrap}
.user-page .table tbody td{padding:14px 16px;border-color:#eef1f5;color:#536079;vertical-align:middle}
.user-page .table tbody tr{transition:background .15s}
.user-page .table tbody tr:hover{background:#f8faff}
.user-page .table td strong{color:#26324b;font-weight:700}
.user-page .user-name-cell{display:flex;align-items:center;gap:10px;min-width:170px}
.user-page .user-name-cell img{width:38px;height:38px;flex:0 0 38px;border:0;border-radius:11px;object-fit:cover;background:#e8f0ff}
.user-page .table-pagination{padding:14px 20px;background:#fff}
.user-page .page-link{border-radius:7px;margin-left:4px!important;font-size:.72rem}
.user-page .role-badge{display:inline-block;padding:5px 9px;border-radius:999px;font-size:.68rem;font-weight:700}
.user-page .role-master{background:#fee2e2;color:#991b1b}
.user-page .role-admin_it{background:#fef3c7;color:#92400e}
.user-page .role-user{background:#dcfce7;color:#166534}
.user-page .role-active{background:#dbeafe;color:#1d4ed8}
.user-page .role-inactive{background:#e2e8f0;color:#475569}
@media(max-width:767px){.user-page>.d-flex{gap:14px;flex-direction:column!important}.user-page>.d-flex .btn{align-self:stretch}.user-page .card-body{padding:15px}.user-page .card-header{padding:14px 15px}.user-page .user-widget{min-height:108px;padding:15px}.user-page .user-widget strong{font-size:1.45rem}}
</style>
@endsection
