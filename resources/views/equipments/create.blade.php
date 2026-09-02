@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-colorful">
        <div class="card-header">Tambah Peralatan IT</div>
        <div class="card-body">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('equipments.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input name="name" class="form-control" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kode Aset / Asset Tag</label>
                <input name="asset_tag" class="form-control" value="{{ old('asset_tag') }}" placeholder="Contoh: IT-LPT-001">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Model</label>
                <input name="model" class="form-control" value="{{ old('model') }}" placeholder="Contoh: ProBook 440 G9">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Sistem Operasi <span class="text-muted">(opsional)</span></label>
            <input name="operating_system" class="form-control" list="operating-system-options" value="{{ old('operating_system') }}" placeholder="Contoh: Windows 11 Pro 23H2">
            <datalist id="operating-system-options">
                <option value="Windows 11 Pro"></option>
                <option value="Windows 10 Pro"></option>
                <option value="Windows Server 2022"></option>
                <option value="Ubuntu Server 22.04 LTS"></option>
                <option value="Ubuntu 22.04 LTS"></option>
                <option value="macOS"></option>
                <option value="VMware ESXi"></option>
                <option value="Tidak berlaku / perangkat jaringan"></option>
            </datalist>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Peralatan <span class="text-muted">(opsional)</span></label>
            <input name="photo" type="file" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">Format JPG, PNG, atau WebP. Maksimal 5 MB.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipe</label>
            <select id="equipmentType" name="equipment_type_id" class="form-control">
                <option value="">-- Pilih --</option>
                @foreach($types as $t)
                    <option value="{{ $t->id }}" data-type-name="{{ strtolower($t->name) }}" {{ old('equipment_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div id="technicalDetails" class="technical-details d-none">
            <div class="technical-details-title">Spesifikasi Berdasarkan Tipe Peralatan</div>
            <div data-type-section="komputer" class="type-fields d-none"><div class="row"><div class="col-md-3 mb-3"><label class="form-label">Processor</label><input name="technical_details[processor]" class="form-control" value="{{ old('technical_details.processor') }}" placeholder="Intel Core i5"></div><div class="col-md-3 mb-3"><label class="form-label">RAM</label><input name="technical_details[ram]" class="form-control" value="{{ old('technical_details.ram') }}" placeholder="16 GB"></div><div class="col-md-3 mb-3"><label class="form-label">Storage</label><input name="technical_details[storage]" class="form-control" value="{{ old('technical_details.storage') }}" placeholder="512 GB SSD"></div><div class="col-md-3 mb-3"><label class="form-label">GPU / VGA</label><input name="technical_details[gpu]" class="form-control" value="{{ old('technical_details.gpu') }}" placeholder="Integrated / RTX"></div></div></div>
            <div data-type-section="monitor" class="type-fields d-none"><div class="row"><div class="col-md-3 mb-3"><label class="form-label">Ukuran Layar</label><input name="technical_details[screen_size]" class="form-control" value="{{ old('technical_details.screen_size') }}" placeholder="24 inch"></div><div class="col-md-3 mb-3"><label class="form-label">Resolusi</label><input name="technical_details[resolution]" class="form-control" value="{{ old('technical_details.resolution') }}" placeholder="1920 x 1080"></div><div class="col-md-3 mb-3"><label class="form-label">Panel</label><input name="technical_details[panel_type]" class="form-control" value="{{ old('technical_details.panel_type') }}" placeholder="IPS / VA / TN"></div><div class="col-md-3 mb-3"><label class="form-label">Port Input</label><input name="technical_details[display_ports]" class="form-control" value="{{ old('technical_details.display_ports') }}" placeholder="HDMI, DisplayPort"></div></div></div>
            <div data-type-section="network" class="type-fields d-none"><div class="row"><div class="col-md-3 mb-3"><label class="form-label">Jumlah Port</label><input name="technical_details[port_count]" class="form-control" value="{{ old('technical_details.port_count') }}" placeholder="24 Port"></div><div class="col-md-3 mb-3"><label class="form-label">Kecepatan Port</label><input name="technical_details[port_speed]" class="form-control" value="{{ old('technical_details.port_speed') }}" placeholder="1 Gbps"></div><div class="col-md-3 mb-3"><label class="form-label">Firmware</label><input name="technical_details[firmware_version]" class="form-control" value="{{ old('technical_details.firmware_version') }}" placeholder="Versi firmware"></div><div class="col-md-3 mb-3"><label class="form-label">MAC Address</label><input name="technical_details[mac_address]" class="form-control" value="{{ old('technical_details.mac_address') }}" placeholder="00:00:00:00:00:00"></div></div></div>
            <div data-type-section="printer" class="type-fields d-none"><div class="row"><div class="col-md-3 mb-3"><label class="form-label">Teknologi Cetak</label><input name="technical_details[print_technology]" class="form-control" value="{{ old('technical_details.print_technology') }}" placeholder="Laser / Inkjet"></div><div class="col-md-3 mb-3"><label class="form-label">Warna / Mono</label><input name="technical_details[print_color]" class="form-control" value="{{ old('technical_details.print_color') }}" placeholder="Warna / Monokrom"></div><div class="col-md-3 mb-3"><label class="form-label">Koneksi</label><input name="technical_details[print_connection]" class="form-control" value="{{ old('technical_details.print_connection') }}" placeholder="USB / LAN / Wi-Fi"></div><div class="col-md-3 mb-3"><label class="form-label">Duplex</label><select name="technical_details[duplex]" class="form-control"><option value="">-- Pilih --</option><option value="Ya" {{ old('technical_details.duplex') === 'Ya' ? 'selected' : '' }}>Ya</option><option value="Tidak" {{ old('technical_details.duplex') === 'Tidak' ? 'selected' : '' }}>Tidak</option></select></div></div></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Serial</label>
            <input name="serial_number" class="form-control">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Manufacturer</label>
                <select name="manufacturer_id" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach($manufacturers as $m)
                            <option value="{{ $m->id }}" {{ old('manufacturer_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Lokasi</label>
                <select name="location_id" class="form-control">
                    <option value="">-- Pilih --</option>
                    @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ old('location_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Vendor / Pemasok</label>
                <input name="vendor_name" class="form-control" value="{{ old('vendor_name') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Akun User / Pemilik Peralatan</label>
                <select name="user_id" class="form-control">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Nama PIC (Kontak)</label>
                <input name="owner_name" class="form-control" value="{{ old('owner_name') }}" placeholder="Nama orang yang bertanggung jawab">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Unit / Departemen</label>
                <input name="department" class="form-control" value="{{ old('department') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kapasitas / Spesifikasi singkat</label>
                <input name="capacity" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tahun Pembuatan / Pembelian</label>
                <input name="manufacture_year" type="number" class="form-control" placeholder="YYYY">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Pembelian</label>
                <input name="purchase_date" type="date" class="form-control" value="{{ old('purchase_date') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Akhir Garansi</label>
                <input name="warranty_expiry" type="date" class="form-control" value="{{ old('warranty_expiry') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Akhir Kontrak Dukungan</label>
                <input name="support_contract_end" type="date" class="form-control" value="{{ old('support_contract_end') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Spesifikasi (detail)</label>
            <textarea name="specification" class="form-control"></textarea>
        </div>
        <div class="row">
                <div class="col-md-6">
                <label class="form-label">IP Address</label>
                <input name="ip_address" class="form-control" placeholder="192.168.x.x" value="{{ old('ip_address') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kondisi</label>
                <select name="condition" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="baik" {{ old('condition')=='baik'?'selected':'' }}>Baik</option>
                    <option value="rusak" {{ old('condition')=='rusak'?'selected':'' }}>Rusak</option>
                    <option value="perbaikan" {{ old('condition')=='perbaikan'?'selected':'' }}>Perbaikan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kritikalitas Layanan</label>
                <select name="criticality" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="low" {{ old('criticality') === 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ old('criticality') === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high" {{ old('criticality') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="critical" {{ old('criticality') === 'critical' ? 'selected' : '' }}>Kritis</option>
                </select>
            </div>
            <div class="col-md-2 align-self-end">
                <button class="btn btn-brand">Simpan</button>
            </div>
        </div>
    </form>
        </div>
    </div>
</div>
<script>
const equipmentTypeInput = document.getElementById('equipmentType');
const technicalDetails = document.getElementById('technicalDetails');
const typeSections = document.querySelectorAll('[data-type-section]');
function updateTechnicalForm() {
    const selected = equipmentTypeInput.options[equipmentTypeInput.selectedIndex];
    const typeName = selected?.dataset.typeName || '';
    let visible = false;
    typeSections.forEach(section => {
        const matches = section.dataset.typeSection === typeName;
        section.classList.toggle('d-none', !matches);
        section.querySelectorAll('input, select').forEach(input => input.disabled = !matches);
        visible = visible || matches;
    });
    technicalDetails.classList.toggle('d-none', !visible);
}
equipmentTypeInput.addEventListener('change', updateTechnicalForm);
updateTechnicalForm();
</script>
<style>.technical-details{margin:0 0 1rem;padding:16px;background:#f5f9fd;border:1px solid #cfe1f2;border-left:4px solid #0b5ea8}.technical-details-title{margin-bottom:14px;color:#0b5ea8;font-size:.9rem;font-weight:700}</style>
@endsection
