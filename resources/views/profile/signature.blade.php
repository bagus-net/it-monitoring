@extends('layouts.app')

@section('content')
<div class="container mt-4 signature-page">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="signature-eyebrow">Akun</div>
            <h2 class="mb-1">Tanda Tangan Digital</h2>
            <p class="text-muted mb-0">Buat template tanda tangan Anda untuk dipakai otomatis pada dokumen cetak tiket perbaikan IT.</p>
        </div>
    </div>
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card signature-card">
                <div class="card-header"><strong>Buat / Perbarui Tanda Tangan</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('signature.update') }}" enctype="multipart/form-data" id="signatureForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="signature_data" id="signatureData">
                        <label class="form-label">Gambar Tanda Tangan</label>
                        <div class="signature-pad-wrap">
                            <canvas id="signaturePad" width="900" height="300"></canvas>
                            <span class="signature-hint">Tanda tangan di area ini menggunakan mouse atau jari</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSignature">Bersihkan</button>
                            <div class="signature-tools">
                                <label class="form-label mb-0">Ketebalan</label>
                                <input type="range" id="penSize" min="1" max="6" value="2" class="form-range">
                                <label class="form-label mb-0">Warna</label>
                                <input type="color" id="penColor" value="#0b2545" class="form-control form-control-color">
                            </div>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Atau Unggah Gambar Tanda Tangan</label>
                                <input type="file" name="signature_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                                <div class="form-text">PNG dengan latar transparan paling rapi. Maksimal 2 MB.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan / Keterangan</label>
                                <input type="text" name="signature_title" value="{{ old('signature_title', $user->signature_title) }}" class="form-control" placeholder="Contoh: Staff IT Support">
                                <div class="form-text">Ditampilkan di bawah nama pada dokumen cetak.</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-brand">Simpan Tanda Tangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card signature-card h-100">
                <div class="card-header"><strong>Tanda Tangan Tersimpan</strong></div>
                <div class="card-body">
                    @if($user->signature_path)
                        <div class="signature-preview">
                            <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Tanda tangan {{ $user->name }}">
                            <strong>{{ $user->name }}</strong>
                            <small>{{ $user->signature_title ?: $user->roleLabel() }}</small>
                        </div>
                        <form method="POST" action="{{ route('signature.destroy') }}" class="mt-3" onsubmit="return confirm('Hapus tanda tangan digital Anda?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm w-100">Hapus Tanda Tangan</button>
                        </form>
                    @else
                        <p class="text-muted mb-0">Belum ada tanda tangan tersimpan. Buat tanda tangan pada panel di samping lalu simpan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .signature-eyebrow{color:#0b5ea8;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em}
    .signature-card{border:1px solid #dbe5ef}
    .signature-card .card-header{background:#f8fafc}
    .signature-pad-wrap{position:relative;border:2px dashed #94a3b8;border-radius:6px;background:#fff}
    .signature-pad-wrap canvas{display:block;width:100%;height:220px;touch-action:none;cursor:crosshair}
    .signature-hint{position:absolute;left:12px;bottom:8px;color:#94a3b8;font-size:.74rem;pointer-events:none}
    .signature-pad-wrap.has-drawing .signature-hint{display:none}
    .signature-tools{display:flex;align-items:center;gap:8px;margin-left:auto}
    .signature-tools .form-label{font-size:.74rem;color:#475569;font-weight:700}
    .signature-tools .form-range{width:110px}
    .signature-preview{padding:14px;border:1px solid #dbe5ef;border-radius:6px;background:#fbfdff;text-align:center}
    .signature-preview img{display:block;width:100%;max-height:170px;object-fit:contain;margin-bottom:8px}
    .signature-preview strong{display:block;font-size:.95rem}
    .signature-preview small{display:block;color:#64748b}
</style>
<script>
    (function () {
        const canvas = document.getElementById('signaturePad');
        const wrap = canvas.closest('.signature-pad-wrap');
        const context = canvas.getContext('2d');
        const penSize = document.getElementById('penSize');
        const penColor = document.getElementById('penColor');
        let drawing = false;
        let hasDrawing = false;

        context.lineCap = 'round';
        context.lineJoin = 'round';

        function pointerPosition(event) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (event.clientX - rect.left) * (canvas.width / rect.width),
                y: (event.clientY - rect.top) * (canvas.height / rect.height),
            };
        }

        function start(event) {
            drawing = true;
            hasDrawing = true;
            wrap.classList.add('has-drawing');
            const point = pointerPosition(event);
            context.beginPath();
            context.moveTo(point.x, point.y);
            canvas.setPointerCapture(event.pointerId);
        }

        function draw(event) {
            if (!drawing) return;
            const point = pointerPosition(event);
            context.strokeStyle = penColor.value;
            context.lineWidth = Number(penSize.value) * (canvas.width / canvas.getBoundingClientRect().width);
            context.lineTo(point.x, point.y);
            context.stroke();
        }

        function stop() { drawing = false; }

        canvas.addEventListener('pointerdown', start);
        canvas.addEventListener('pointermove', draw);
        canvas.addEventListener('pointerup', stop);
        canvas.addEventListener('pointerleave', stop);

        document.getElementById('clearSignature').addEventListener('click', () => {
            context.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawing = false;
            wrap.classList.remove('has-drawing');
        });

        document.getElementById('signatureForm').addEventListener('submit', () => {
            if (hasDrawing) {
                document.getElementById('signatureData').value = canvas.toDataURL('image/png');
            }
        });
    })();
</script>
@endsection
