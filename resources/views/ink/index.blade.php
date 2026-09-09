@extends('layouts.app')

@section('content')
<div class="container mt-4 ink-page">
    <div class="ink-hero mb-4">
        <div><div class="ink-eyebrow">OPERASIONAL IT</div><h1>Kelola Tinta</h1><p>Kontrol stok tinta, pencatatan pemakaian, dan riwayat pengisian dalam satu tempat.</p></div>
        <div class="ink-hero-mark">INK<br><span>STOCK CONTROL</span></div>
    </div>

    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="ink-stat ink-stat-blue"><span>Jenis Tinta</span><strong>{{ $summary['types'] }}</strong><small>master terdaftar</small></div></div>
        <div class="col-6 col-lg-3"><div class="ink-stat ink-stat-cyan"><span>Total Stok</span><strong>{{ $summary['units'] }}</strong><small>unit tersedia</small></div></div>
        <div class="col-6 col-lg-3"><div class="ink-stat ink-stat-orange"><span>Stok Menipis</span><strong>{{ $summary['low_stock'] }}</strong><small>perlu diperiksa</small></div></div>
        <div class="col-6 col-lg-3"><div class="ink-stat ink-stat-green"><span>Transaksi</span><strong>{{ $summary['transactions'] }}</strong><small>catatan tersimpan</small></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <section class="card ink-panel h-100">
                <div class="card-header"><span class="panel-kicker">MASTER DATA</span><h2>{{ $editing ? 'Edit Jenis Tinta' : 'Tambah Jenis Tinta' }}</h2><p>Definisikan cartridge atau botol tinta yang digunakan tim IT.</p></div>
                <div class="card-body"><form method="POST" action="{{ $editing ? route('ink.types.update', $editing) : route('ink.types.store') }}" class="row g-3">@csrf @if($editing) @method('PUT') @endif
                    <div class="col-12"><label class="form-label">Nama Jenis Tinta</label><input name="name" class="form-control" value="{{ old('name', $editing?->name) }}" placeholder="Contoh: HP 682 Black" required></div>
                    <div class="col-md-6"><label class="form-label">Merek</label><input name="brand" class="form-control" value="{{ old('brand', $editing?->brand) }}" placeholder="HP, Canon, Epson"></div>
                    <div class="col-md-6"><label class="form-label">Warna</label><input name="color" class="form-control" value="{{ old('color', $editing?->color) }}" placeholder="Black / Cyan"></div>
                    <div class="col-md-6"><label class="form-label">Satuan</label><input name="unit" class="form-control" value="{{ old('unit', $editing?->unit ?: 'pcs') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Batas Minimum</label><input type="number" name="minimum_stock" class="form-control" min="0" value="{{ old('minimum_stock', $editing?->minimum_stock ?? 0) }}" required></div>
                    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $editing?->notes) }}</textarea></div>
                    <div class="col-12 d-flex gap-2"><button class="btn btn-brand">{{ $editing ? 'Simpan Perubahan' : 'Tambah Master' }}</button>@if($editing)<a href="{{ route('ink.index') }}" class="btn btn-outline-secondary">Batal</a>@endif</div>
                </form></div>
            </section>
        </div>
        <div class="col-xl-7">
            <section class="card ink-panel h-100">
                <div class="card-header"><span class="panel-kicker">PERGERAKAN STOK</span><h2>Catat Transaksi</h2><p>Gunakan <strong>Stok Masuk</strong> untuk pengisian dan <strong>Stok Keluar</strong> untuk pemakaian.</p></div>
                <div class="card-body"><form method="POST" action="{{ route('ink.transactions.store') }}" class="row g-3">@csrf
                    <div class="col-md-6"><label class="form-label">Jenis Tinta</label><select name="ink_type_id" class="form-select" required><option value="">-- Pilih Jenis Tinta --</option>@foreach($inkTypes as $ink)<option value="{{ $ink->id }}">{{ $ink->name }}{{ $ink->brand ? ' - ' . $ink->brand : '' }} | Stok: {{ $ink->current_stock }} {{ $ink->unit }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Printer Tujuan</label><select name="equipment_id" class="form-select"><option value="">-- Pilih Printer --</option>@foreach($printers as $printer)<option value="{{ $printer->id }}">{{ $printer->name }} - {{ $printer->owner_name ?: ($printer->owner->name ?? 'PIC belum diisi') }} - {{ $printer->assetLocation->name ?? $printer->getRawOriginal('location') ?: 'Lokasi belum diisi' }}</option>@endforeach</select><div class="form-text">Format: Nama Printer - Nama PIC - Lokasi.</div></div>
                    <div class="col-md-5"><label class="form-label">Jenis Transaksi</label><select name="type" class="form-select" required><option value="in">Stok Masuk</option><option value="out">Stok Keluar</option></select></div>
                    <div class="col-md-4"><label class="form-label">Jumlah</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                    <div class="col-md-8"><label class="form-label">Tanggal</label><input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="col-md-6"><label class="form-label">Nomor Referensi</label><input name="reference" class="form-control" placeholder="Nota / PO / dokumen"></div>
                    <div class="col-md-6"><label class="form-label">Pemakai / Penerima</label><input name="recipient" class="form-control" placeholder="Nama user atau bagian"></div>
                    <div class="col-12"><label class="form-label">Keterangan</label><textarea name="notes" class="form-control" rows="2" placeholder="Keterangan pengisian atau pemakaian"></textarea></div>
                    <div class="col-12"><button class="btn btn-ink">Simpan Transaksi</button></div>
                </form></div>
            </section>
        </div>
    </div>

    <section class="card ink-panel mb-4"><div class="card-header d-flex justify-content-between align-items-center gap-3"><div><span class="panel-kicker">MASTER JENIS TINTA</span><h2 class="mb-0">Daftar Stok</h2></div><form method="GET" action="{{ route('ink.index') }}" class="ink-search"><input name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari tinta..."><button class="btn btn-sm btn-outline-primary">Cari</button></form></div><div class="table-responsive"><table class="table ink-table align-middle mb-0"><thead><tr><th>Jenis</th><th>Warna</th><th>Stok</th><th>Minimum</th><th>Status</th><th>Aksi</th></tr></thead><tbody>@forelse($inkTypes as $ink)<tr><td><strong>{{ $ink->name }}</strong><small class="d-block text-muted">{{ $ink->brand ?: 'Merek belum dicatat' }} · {{ $ink->transactions_count }} transaksi</small></td><td>{{ $ink->color ?: '-' }}</td><td><strong class="stock-number">{{ $ink->current_stock }}</strong> {{ $ink->unit }}</td><td>{{ $ink->minimum_stock }} {{ $ink->unit }}</td><td>@if($ink->is_low_stock)<span class="ink-badge ink-low">Stok Menipis</span>@else<span class="ink-badge ink-ready">Aman</span>@endif</td><td class="text-nowrap"><a href="{{ route('ink.index', ['edit' => $ink->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a><form method="POST" action="{{ route('ink.types.destroy', $ink) }}" class="d-inline" onsubmit="return confirm('Hapus jenis tinta ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">Belum ada jenis tinta.</td></tr>@endforelse</tbody></table></div><div class="table-pagination">{{ $inkTypes->links() }}</div></section>

    <section class="card ink-panel"><div class="card-header"><span class="panel-kicker">AUDIT STOK</span><h2>Riwayat Pengisian &amp; Pemakaian</h2><p class="mb-0">Setiap transaksi menyimpan saldo sebelum dan sesudah perubahan.</p></div><div class="table-responsive"><table class="table ink-table align-middle mb-0"><thead><tr><th>Tanggal</th><th>Jenis Tinta</th><th>Printer Tujuan</th><th>Transaksi</th><th>Jumlah</th><th>Saldo</th><th>Referensi / Keterangan</th><th>Petugas</th></tr></thead><tbody>@forelse($transactions as $transaction)<tr><td>{{ $transaction->transaction_date?->format('d M Y') }}</td><td><strong>{{ $transaction->inkType->name }}</strong><small class="d-block text-muted">{{ $transaction->inkType->brand ?: '-' }}</small></td><td>@if($transaction->equipment)<strong>{{ $transaction->equipment->name }}</strong><small class="d-block text-muted">{{ $transaction->equipment->owner_name ?: ($transaction->equipment->owner->name ?? 'PIC belum diisi') }} - {{ $transaction->equipment->assetLocation->name ?? $transaction->equipment->getRawOriginal('location') ?: 'Lokasi belum diisi' }}</small>@else Tidak dikaitkan @endif</td><td>@if($transaction->type === 'in')<span class="ink-badge ink-in">+ Stok Masuk</span>@else<span class="ink-badge ink-out">- Stok Keluar</span>@endif</td><td><strong>{{ $transaction->quantity }}</strong> {{ $transaction->inkType->unit }}</td><td>{{ $transaction->stock_before }} &rarr; <strong>{{ $transaction->stock_after }}</strong></td><td>{{ $transaction->reference ?: '-' }}@if($transaction->recipient)<small class="d-block text-muted">Penerima: {{ $transaction->recipient }}</small>@endif @if($transaction->notes)<small class="d-block text-muted">{{ $transaction->notes }}</small>@endif</td><td>{{ $transaction->creator->name ?? '-' }}</td></tr>@empty<tr><td colspan="8" class="text-center text-muted py-4">Belum ada transaksi stok.</td></tr>@endforelse</tbody></table></div><div class="table-pagination">{{ $transactions->links('pagination::bootstrap-5') }}</div></section>
</div>
<style>
.ink-page{color:#17324d}.ink-hero{display:flex;justify-content:space-between;align-items:center;gap:24px;padding:28px 32px;border-radius:10px;background:linear-gradient(120deg,#0b5ea8,#123b67);color:#fff;overflow:hidden}.ink-eyebrow,.panel-kicker{font-size:.7rem;font-weight:800;letter-spacing:.13em}.ink-hero h1{margin:5px 0;font-size:2rem}.ink-hero p{margin:0;max-width:560px;color:#dbeafe}.ink-hero-mark{min-width:155px;padding:18px;border:1px solid rgba(255,255,255,.35);border-radius:8px;text-align:center;font-size:1.5rem;font-weight:800;letter-spacing:.08em}.ink-hero-mark span{font-size:.58rem;letter-spacing:.12em}.ink-stat{padding:16px 18px;border:1px solid #dbe5ef;border-top:4px solid #125ea8;background:#fff}.ink-stat span,.ink-stat small{display:block;color:#64748b;font-size:.76rem}.ink-stat strong{display:block;margin:2px 0;font-size:1.65rem}.ink-stat-cyan{border-top-color:#0891b2}.ink-stat-orange{border-top-color:#f59e0b}.ink-stat-green{border-top-color:#16a34a}.ink-panel{border:1px solid #dbe5ef;box-shadow:0 4px 14px rgba(23,50,77,.04)}.ink-panel .card-header{padding:17px 20px;background:#f8fbfe;border-bottom:1px solid #e5edf4}.ink-panel .card-header h2{margin:4px 0 3px;font-size:1.08rem;color:#125ea8}.ink-panel .card-header p{margin:0;color:#64748b;font-size:.84rem}.panel-kicker{color:#0b5ea8}.ink-panel .card-body{padding:20px}.ink-panel .form-label{margin-bottom:4px;color:#475569;font-size:.78rem;font-weight:700}.btn-ink{background:#125ea8;color:#fff;font-weight:700}.btn-ink:hover{background:#0b4d8a;color:#fff}.ink-search{display:flex;gap:7px;width:280px}.ink-table{font-size:.86rem}.ink-table thead th{background:#f1f5f9;color:#475569;font-size:.76rem;text-transform:uppercase;letter-spacing:.03em}.stock-number{color:#125ea8;font-size:1.05rem}.ink-badge{display:inline-block;padding:4px 7px;border-radius:4px;font-size:.7rem;font-weight:800}.ink-low,.ink-out{background:#fee2e2;color:#991b1b}.ink-ready,.ink-in{background:#dcfce7;color:#166534}@media(max-width:700px){.ink-hero{padding:22px;align-items:flex-start}.ink-hero-mark{display:none}.ink-search{width:100%}.ink-panel .card-header{align-items:flex-start!important;flex-direction:column}}
</style>
<style>
@media(max-width:700px){
    .ink-page{max-width:430px;margin:0 auto!important;padding:0 12px 84px;color:#172039}
    .ink-hero{margin:0 0 16px!important;padding:18px 20px;border-radius:18px;background:linear-gradient(135deg,#2563eb,#38a5f5);box-shadow:0 10px 26px rgba(37,99,235,.18)}
    .ink-hero h1{font-size:1.75rem;line-height:1.05}.ink-hero p{font-size:.8rem;line-height:1.45}.ink-eyebrow{font-size:.62rem;letter-spacing:.12em}.ink-hero-mark{display:none}
    .ink-page>.row.g-3{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-bottom:16px!important}
    .ink-page>.row.g-3>[class*=col-]{width:auto;padding:0}
    .ink-stat{min-height:118px;padding:17px 16px;border:1px solid #e7ebf2;border-top:0;border-radius:14px;background:#fff;box-shadow:0 7px 18px rgba(35,52,85,.055);position:relative;overflow:hidden}
    .ink-stat:after{content:'';position:absolute;right:-25px;bottom:-36px;width:86px;height:86px;border-radius:50%;background:#eef2f7}.ink-stat span{font-size:.68rem;font-weight:700}.ink-stat strong{margin:9px 0 3px;font-size:1.65rem;color:#0f172a}.ink-stat small{font-size:.65rem}
    .ink-page>.row.g-4{display:block;margin:0 0 14px!important}
    .ink-page>.row.g-4>[class*=col-]{padding:0;margin-bottom:10px}
    .ink-page>.row.g-4 .ink-panel{border-radius:16px;overflow:hidden}
    .ink-page>.row.g-4 .ink-panel .card-header{position:relative;padding:14px 16px;background:linear-gradient(135deg,#fff,#f0fdfa)}
    .ink-page>.row.g-4 .ink-panel .card-header h2{font-size:.9rem}.ink-page>.row.g-4 .ink-panel .card-header p{font-size:.68rem;line-height:1.3}
    .ink-page>.row.g-4 .ink-panel .card-body{padding:14px 16px}.ink-panel .form-label{font-size:.64rem;text-transform:uppercase;letter-spacing:.04em}.ink-panel .form-control,.ink-panel .form-select{min-height:36px;border-radius:10px;font-size:.72rem}.ink-panel textarea.form-control{min-height:64px}
    .ink-panel .btn-brand,.ink-panel .btn-ink{width:100%;border-radius:11px;padding:10px;font-size:.75rem}
    .ink-panel{border-radius:16px;box-shadow:0 7px 20px rgba(35,52,85,.055);overflow:hidden}.ink-panel .card-header{padding:14px 16px}.ink-panel .card-header h2{font-size:.92rem}.ink-panel .card-header p{font-size:.68rem;line-height:1.3}.panel-kicker{font-size:.58rem;letter-spacing:.12em}
    .ink-search{width:100%;margin-top:8px}.ink-search .form-control{min-height:34px;border-radius:10px;font-size:.72rem}.ink-search .btn{border-radius:10px;font-size:.68rem}
    .ink-table{min-width:760px;font-size:.7rem}.ink-table thead th{padding:9px 10px;font-size:.6rem}.ink-table tbody td{padding:9px 10px}.ink-badge{border-radius:999px;font-size:.58rem}.stock-number{font-size:.9rem}.table-pagination{padding:10px 14px!important}
    .ink-page section.ink-panel{margin-bottom:12px!important}.ink-page section.ink-panel:last-of-type{margin-bottom:0!important}
}
@media(max-width:420px){.ink-page{padding-left:10px;padding-right:10px}.ink-stat{min-height:106px;padding:14px}.ink-stat strong{font-size:1.45rem}.ink-page>.row.g-4 .ink-panel .card-body{padding:13px}}
@media(max-width:700px){
    .ink-page>.row.g-3{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;justify-content:stretch;gap:8px!important;margin-bottom:10px!important;padding:1px 0 8px}
    .ink-page>.row.g-3>[class*=col-]{width:auto!important;padding:0;min-width:0}
    .ink-stat{min-height:78px!important;padding:10px 9px!important;border-radius:12px;text-align:left}
    .ink-stat:after{right:-30px;bottom:-42px;width:82px;height:82px}
    .ink-stat span{font-size:.55rem!important;line-height:1.1}
    .ink-stat strong{margin:5px 0 2px!important;font-size:1.12rem!important;line-height:1}
    .ink-stat small{font-size:.5rem!important;line-height:1.1}
    .ink-page>.row.g-4{margin-bottom:9px!important}
    .ink-page>.row.g-4 .ink-panel .card-header{padding:9px 12px!important}
    .ink-page>.row.g-4 .ink-panel .card-header h2{font-size:.76rem!important;margin:2px 0!important}
    .ink-page>.row.g-4 .ink-panel .card-header p{font-size:.58rem!important;line-height:1.2!important}
    .ink-page>.row.g-4 .ink-panel .card-body{padding:10px 12px!important}
    .ink-panel .form-label{font-size:.55rem!important;margin-bottom:2px!important}
    .ink-panel .form-control,.ink-panel .form-select{min-height:30px!important;border-radius:8px!important;font-size:.64rem!important;padding:4px 8px!important}
    .ink-panel textarea.form-control{min-height:48px!important}
    .ink-panel .btn-brand,.ink-panel .btn-ink{padding:7px 9px!important;border-radius:9px!important;font-size:.66rem!important}
    .ink-page section.ink-panel{margin-bottom:9px!important}
    .ink-panel .card-header{padding:10px 12px!important}.ink-panel .card-header h2{font-size:.8rem!important}.ink-panel .card-header p{font-size:.6rem!important}
    .ink-page>.row.g-4{display:none!important}
    .ink-page .quick-actions,
    .ink-page .quick-action-list,
    .ink-page .quick-links{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:8px!important}
    .ink-page .quick-actions a,
    .ink-page .quick-actions button,
    .ink-page .quick-action-list a,
    .ink-page .quick-action-list button,
    .ink-page .quick-links a,
    .ink-page .quick-links button{min-height:48px!important;padding:8px 10px!important;border-radius:12px!important;font-size:.65rem!important;line-height:1.15!important}
    .ink-page .quick-actions small,
    .ink-page .quick-action-list small,
    .ink-page .quick-links small{font-size:.55rem!important;line-height:1.1!important}
    .ink-page .crud-action-bar{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:8px!important;padding:10px!important;border-radius:14px!important;margin:0 0 10px!important;overflow:hidden!important}
    .ink-page .crud-action-bar>div:first-child{grid-column:1/-1;margin:0!important}
    .ink-page .crud-action-bar>div:first-child small{display:none!important}
    .ink-page .crud-action-bar>div:first-child strong{font-size:.72rem!important;line-height:1.1!important}
    .ink-page .crud-action-kicker{font-size:.52rem!important;letter-spacing:.1em!important}
    .ink-page .crud-action-button{width:100%!important;min-width:0!important;min-height:50px!important;padding:8px!important;display:grid!important;grid-template-columns:22px minmax(0,1fr) 12px!important;gap:5px!important;align-items:center!important;border-radius:12px!important;overflow:hidden!important}
    .ink-page .crud-action-button>i:first-child{width:22px!important;height:22px!important;display:grid!important;place-items:center!important;border-radius:7px!important;font-size:.75rem!important}
    .ink-page .crud-action-button>i:last-child{font-size:.7rem!important;justify-self:end!important}
    .ink-page .crud-action-button span{min-width:0!important}.ink-page .crud-action-button strong{font-size:.64rem!important;line-height:1.08!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important}.ink-page .crud-action-button small{font-size:.5rem!important;line-height:1!important}
    .ink-page section.ink-panel .table-responsive{overflow-x:auto!important;-webkit-overflow-scrolling:touch}
    .ink-page section.ink-panel:first-of-type .table-responsive{overflow-x:hidden!important}
    .ink-page section.ink-panel:first-of-type .ink-table{min-width:0!important;width:100%!important;table-layout:fixed;font-size:.66rem!important}
    .ink-page section.ink-panel:first-of-type .ink-table th,
    .ink-page section.ink-panel:first-of-type .ink-table td{padding:7px 4px!important;vertical-align:middle}
    .ink-page section.ink-panel:first-of-type .ink-table th:nth-child(1),
    .ink-page section.ink-panel:first-of-type .ink-table td:nth-child(1){width:26px!important;text-align:center}
    .ink-page section.ink-panel:first-of-type .ink-table th:nth-child(2),
    .ink-page section.ink-panel:first-of-type .ink-table td:nth-child(2){width:auto!important}
    .ink-page section.ink-panel:first-of-type .ink-table th:nth-child(3),
    .ink-page section.ink-panel:first-of-type .ink-table td:nth-child(3){width:46px!important;text-align:center}
    .ink-page section.ink-panel:first-of-type .ink-table th:nth-child(4),
    .ink-page section.ink-panel:first-of-type .ink-table td:nth-child(4),
    .ink-page section.ink-panel:first-of-type .ink-table th:nth-child(5),
    .ink-page section.ink-panel:first-of-type .ink-table td:nth-child(5){width:43px!important;text-align:center}
    .ink-page section.ink-panel:first-of-type .ink-table th:nth-child(6),
    .ink-page section.ink-panel:first-of-type .ink-table td:nth-child(6){width:56px!important;text-align:center}
    .ink-page section.ink-panel:first-of-type .ink-table th{font-size:.43rem!important;letter-spacing:0!important}
    .ink-page section.ink-panel:first-of-type .ink-table td{font-size:.5rem!important;line-height:1.12!important}
    .ink-page section.ink-panel:first-of-type .ink-table td strong{font-size:.52rem!important;line-height:1.1}
    .ink-page section.ink-panel:first-of-type .ink-table td small{font-size:.42rem!important;line-height:1.05}
    .ink-page section.ink-panel:first-of-type .ink-badge{padding:3px 4px!important;font-size:.39rem!important;border-radius:6px!important;white-space:nowrap}
    .ink-page section.ink-panel:first-of-type .btn-sm{padding:3px 4px!important;font-size:.42rem!important;border-radius:6px!important}
    .ink-page section.ink-panel:first-of-type .ink-table th:last-child,
    .ink-page section.ink-panel:first-of-type .ink-table td:last-child{width:82px!important;text-align:left!important}
    .ink-page section.ink-panel:first-of-type .ink-table td:last-child{display:flex!important;align-items:center!important;gap:3px!important;flex-wrap:wrap!important}
    .ink-page section.ink-panel:first-of-type .ink-table td:last-child .crud-detail-button{order:-1!important;display:inline-flex!important;align-items:center!important;gap:2px!important;color:#475569!important;border-color:#cbd5e1!important;background:#fff!important}
    .ink-page section.ink-panel:first-of-type .ink-table td:last-child .btn-sm{min-width:0!important;white-space:nowrap!important;line-height:1!important}
    .ink-page section.ink-panel:last-of-type .ink-table{min-width:920px!important}
    .ink-page section.ink-panel:last-of-type .ink-table th:last-child,
    .ink-page section.ink-panel:last-of-type .ink-table td:last-child{width:105px!important;text-align:center!important;white-space:normal!important}
    .ink-page section.ink-panel:last-of-type .ink-table td:last-child{display:flex!important;flex-direction:column!important;align-items:center!important;gap:5px!important}
    .ink-page section.ink-panel:last-of-type .ink-table td:last-child .crud-detail-button{display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:3px!important;margin:0!important;padding:4px 8px!important;border-radius:8px!important;font-size:.62rem!important;background:#fff!important;white-space:nowrap!important}
}
</style>
@endsection
