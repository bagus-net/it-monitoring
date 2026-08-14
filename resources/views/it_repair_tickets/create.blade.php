@extends('layouts.app')

@section('content')
<div class="container mt-4">
	<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Buat Tiket Perbaikan IT</h2><p class="text-muted mb-0">Form ini diisi oleh user untuk melaporkan gangguan peralatan IT.</p></div><a href="{{ route('it-repair-tickets.index') }}" class="btn btn-outline-secondary">Kembali</a></div>
	@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
	<form method="POST" action="{{ route('it-repair-tickets.store') }}" enctype="multipart/form-data">@csrf
		<div class="card repair-form"><div class="card-header">FORM TIKET USER - PERMINTAAN PERBAIKAN IT</div><div class="card-body"><div class="row g-3">
			<div class="col-md-6"><label class="form-label">Peralatan IT</label><select name="equipment_id" class="form-select"><option value="">-- Pilih Peralatan --</option>@foreach($equipment as $item)<option value="{{ $item->id }}" {{ (string) old('equipment_id') === (string) $item->id ? 'selected' : '' }}>{{ $item->name }}{{ $item->asset_tag ? ' - ' . $item->asset_tag : '' }}</option>@endforeach</select></div>
			<div class="col-md-3"><label class="form-label">Jenis Peralatan</label><select id="equipmentCategory" name="equipment_category" class="form-select"><option value="">-- Pilih Jenis --</option>@foreach(['Komputer','Laptop','Printer','Monitor','Keyboard','Mouse','Jaringan / Router','CCTV','Proyektor','Scanner','UPS','Server','CMS / ERP','Lainnya'] as $category)<option value="{{ $category }}" {{ old('equipment_category') === $category ? 'selected' : '' }}>{{ $category }}</option>@endforeach</select></div>
			<div class="col-md-3"><label class="form-label">Jenis Error</label><select id="errorType" name="error_type" class="form-select" disabled><option value="">Pilih jenis peralatan dahulu</option></select></div>
			<div class="col-md-4"><label class="form-label">Bagian / Departemen</label><input name="department" class="form-control" value="{{ old('department') }}" placeholder="Contoh: XPDC / IKO"></div><div class="col-md-4"><label class="form-label">Dilaporkan Oleh</label><input name="reported_by" class="form-control" value="{{ old('reported_by') }}"></div><div class="col-md-4"><label class="form-label">Tanggal & Jam Lapor</label><input type="datetime-local" name="reported_at" class="form-control" value="{{ old('reported_at', now()->format('Y-m-d\TH:i')) }}" required></div>
			<div class="col-md-4"><label class="form-label">Prioritas</label><select name="priority" class="form-select"><option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Rendah</option><option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option><option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Tinggi</option><option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Mendesak</option></select></div>
			<div class="col-md-8"><label class="form-label">Foto Error / Lampiran <span class="text-muted">(opsional)</span></label><input type="file" name="error_photo" class="form-control" accept="image/jpeg,image/png,image/webp"><div class="form-text">Upload foto error, screenshot, atau bukti gangguan. JPG, PNG, WebP maksimal 5 MB.</div></div>
			<div class="col-12"><label class="form-label">Keluhan / Kerusakan</label><textarea name="problem_description" class="form-control" rows="4" required placeholder="Jelaskan gangguan atau kerusakan yang dialami">{{ old('problem_description') }}</textarea></div>
		</div></div><div class="card-footer d-flex justify-content-between align-items-center"><small class="text-muted">Status tiket akan otomatis menjadi Open.</small><button class="btn btn-brand">Kirim Tiket</button></div></div>
	</form>
</div>
<script>
const errorOptions = {
	'Komputer': ['Komputer tidak menyala','Komputer restart sendiri','Komputer hang/freeze','Komputer lambat','Layar/monitor tidak tampil','Keyboard tidak berfungsi','Mouse tidak berfungsi','Tidak bisa terhubung internet','Wi-Fi tidak berfungsi','USB/port tidak berfungsi','Sistem operasi error','Aplikasi tidak bisa dibuka','Printer tidak terdeteksi','Suara/audio tidak keluar','Overheat/panas berlebih'],
	'Laptop': ['Laptop tidak menyala','Laptop mati sendiri','Laptop restart sendiri','Laptop lambat','Layar tidak tampil','Layar berkedip','Keyboard tidak berfungsi','Touchpad tidak berfungsi','Baterai tidak mengisi','Baterai cepat habis','Charger tidak berfungsi','Wi-Fi tidak terhubung','Bluetooth tidak berfungsi','Suara/audio tidak keluar','Laptop overheat'],
	'Printer': ['Printer tidak menyala','Printer tidak terdeteksi','Tidak bisa mencetak','Hasil cetak buram','Hasil cetak bergaris','Hasil cetak tidak lengkap','Kertas macet','Tidak bisa menarik kertas','Tinta/toner tidak terdeteksi','Tinta/toner habis','Printer lambat','Hasil cetak tidak sesuai','Printer offline','Error cartridge'],
	'Monitor': ['Monitor tidak menyala','Layar tidak tampil','Layar berkedip','Layar bergaris','Layar buram','Warna tidak normal','Resolusi tidak sesuai','Tidak ada sinyal','HDMI tidak berfungsi','DisplayPort tidak berfungsi','Layar mati sendiri'],
	'Keyboard': ['Keyboard tidak berfungsi','Beberapa tombol tidak berfungsi','Tombol macet','Tombol mengetik sendiri','Keyboard tidak terdeteksi','Koneksi USB bermasalah','Koneksi wireless bermasalah'],
	'Mouse': ['Mouse tidak berfungsi','Cursor tidak bergerak','Klik kiri tidak berfungsi','Klik kanan tidak berfungsi','Scroll tidak berfungsi','Mouse tidak terdeteksi','Koneksi USB bermasalah','Baterai mouse habis'],
	'Jaringan / Router': ['Tidak ada koneksi internet','Internet lambat','Wi-Fi tidak muncul','Wi-Fi tidak bisa terhubung','Koneksi sering terputus','Router tidak menyala','Lampu indikator error','LAN tidak terhubung','IP address bermasalah','Tidak bisa mengakses server'],
	'CCTV': ['Kamera tidak tampil','Kamera mati','Gambar buram','Gambar berkedip','Rekaman tidak tersimpan','Tidak bisa mengakses CCTV','DVR/NVR tidak menyala','Hard disk tidak terdeteksi','Kamera offline','Tidak ada koneksi jaringan'],
	'Proyektor': ['Proyektor tidak menyala','Tidak ada tampilan','Tampilan buram','Tampilan gelap','Warna tidak normal','Tidak ada sinyal','HDMI tidak terdeteksi','Proyektor overheat','Lampu proyektor bermasalah','Proyektor mati sendiri'],
	'Scanner': ['Scanner tidak menyala','Scanner tidak terdeteksi','Tidak bisa melakukan scan','Hasil scan buram','Hasil scan bergaris','Hasil scan miring','Paper jam','Scanner lambat','Koneksi USB bermasalah'],
	'UPS': ['UPS tidak menyala','UPS tidak mengisi','Baterai UPS lemah','UPS berbunyi terus','UPS mati mendadak','Tegangan tidak stabil','Beban berlebih','Baterai perlu diganti'],
	'Server': ['Server tidak menyala','Server down','Server restart sendiri','Server lambat','Storage penuh','Hard disk error','RAID error','Tidak bisa terhubung ke server','Service tidak berjalan','Database error','Network error','Overheat'],
	'CMS / ERP': ['Tidak bisa login','Akun terkunci','Hak akses ditolak','Halaman aplikasi tidak bisa dibuka','Aplikasi lambat','Aplikasi error / crash','Data tidak tampil','Data tidak tersimpan','Transaksi gagal','Laporan tidak bisa dibuat','Cetak dokumen gagal','Sinkronisasi data gagal','Integrasi API gagal','Database error','Session habis sendiri','Upload file gagal'],
	'Lainnya': ['Lainnya']
};
const categoryInput = document.getElementById('equipmentCategory');
const errorInput = document.getElementById('errorType');
const previousError = @json(old('error_type'));
function updateErrorOptions(selectedError = '') {
	const errors = errorOptions[categoryInput.value] || [];
	errorInput.innerHTML = '';
	errorInput.append(new Option(errors.length ? '-- Pilih Jenis Error --' : 'Pilih jenis peralatan dahulu', ''));
	errors.forEach(error => errorInput.append(new Option(error, error, false, error === selectedError)));
	errorInput.disabled = errors.length === 0;
}
categoryInput.addEventListener('change', () => updateErrorOptions());
updateErrorOptions(previousError);
</script>
<style>.repair-form{border:1px solid #dbe5ef}.repair-form .card-header{background:#fff6e4;color:#17324d;font-weight:700}</style>
@endsection
