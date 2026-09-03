@extends('layouts.app')

@section('content')
@php($canManage = auth()->user()->isMaster() || auth()->user()->isAdminIt())
<div class="container mt-4 iso-document-detail">
	<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
		<div><div class="text-primary small fw-bold">{{ $isoDocument->document_number }}</div><h2 class="mb-1"><i class="bi bi-folder2 me-2 text-warning"></i>{{ $isoDocument->title }}</h2><p class="text-muted mb-0">{{ $isoDocument->category }} · {{ $isoDocument->document_date?->format('d F Y') ?? 'Tanggal belum dicatat' }}</p></div>
		<div class="d-flex flex-wrap gap-2">
			@if($canManage)<a href="{{ route('iso-documents.edit', $isoDocument) }}" class="btn btn-outline-primary">Edit</a>@endif
			<a href="{{ route('iso-documents.index') }}" class="btn btn-outline-secondary">Kembali</a>
		</div>
	</div>
	<div class="card mb-3"><div class="card-body"><dl class="row mb-0"><dt class="col-md-3">Revisi</dt><dd class="col-md-9">{{ $isoDocument->revision ?: '-' }}</dd><dt class="col-md-3">Deskripsi</dt><dd class="col-md-9">{!! nl2br(e($isoDocument->description ?: '-')) !!}</dd><dt class="col-md-3">Dibagikan Oleh</dt><dd class="col-md-9">{{ $isoDocument->creator->name ?? '-' }}</dd>@if($canManage)<dt class="col-md-3">Pengguna Berizin</dt><dd class="col-md-9">{{ $isoDocument->permittedUsers->pluck('name')->join(', ') }}</dd>@endif</dl></div></div>

	<div class="card iso-folder-card">
		<div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2"><strong><i class="bi bi-folder2-open"></i> Isi Folder ({{ $isoDocument->files->count() }} file)</strong>@if($canManage)<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#uploadFilePanel"><i class="bi bi-upload"></i> Tambah File</button>@endif</div>
		@if($canManage)
		<div id="uploadFilePanel" class="collapse"><div class="card-body border-bottom bg-light">
			<form method="POST" action="{{ route('iso-documents.files.store', $isoDocument) }}" enctype="multipart/form-data" class="row g-2 align-items-end">
				@csrf
				<div class="col-md-8"><label class="form-label">Pilih File</label><input type="file" name="document_files[]" class="form-control @error('document_files.*') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" multiple required><small class="d-block text-muted">Bisa pilih lebih dari satu file sekaligus. Maks. 20 MB per file.</small>@error('document_files.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
				<div class="col-md-4"><button class="btn btn-brand w-100">Unggah ke Folder</button></div>
			</form>
		</div></div>
		@endif
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table align-middle mb-0 iso-file-table">
					<thead><tr><th>File</th><th>Ukuran</th><th>Diunggah Oleh</th><th>Tanggal</th><th class="text-end">Aksi</th></tr></thead>
					<tbody>
						@forelse($isoDocument->files as $file)
							@php($ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)))
							@php($previewable = in_array($ext, ['pdf', 'xls', 'xlsx'], true))
							<tr>
								<td><div class="iso-file-name"><i class="bi bi-file-earmark-{{ in_array($ext, ['pdf']) ? 'pdf' : (in_array($ext, ['xls','xlsx']) ? 'excel' : (in_array($ext, ['doc','docx']) ? 'word' : (in_array($ext, ['ppt','pptx']) ? 'ppt' : (in_array($ext, ['jpg','jpeg','png']) ? 'image' : 'text')))) }} file-icon-{{ $ext }}"></i><span>{{ $file->file_name }}</span></div></td>
								<td>{{ $file->file_size ? number_format($file->file_size / 1024, 0) . ' KB' : '-' }}</td>
								<td>{{ $file->uploadedBy->name ?? '-' }}</td>
								<td>{{ $file->created_at->format('d M Y H:i') }}</td>
								<td class="text-end text-nowrap">
									@if($previewable)<button type="button" class="btn btn-sm btn-outline-primary iso-preview-btn" data-name="{{ $file->file_name }}" data-type="{{ $ext }}" data-src="{{ route('iso-documents.files.preview', [$isoDocument, $file]) }}"><i class="bi bi-eye"></i></button>@endif
									<a href="{{ route('iso-documents.files.download', [$isoDocument, $file]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
									@if($canManage)<form method="POST" action="{{ route('iso-documents.files.destroy', [$isoDocument, $file]) }}" class="d-inline" onsubmit="return confirm('Hapus file ini dari folder?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>@endif
								</td>
							</tr>
						@empty
							<tr><td colspan="5" class="text-center text-muted py-4">Folder ini masih kosong. Tambahkan file untuk mulai menyimpan dokumen.</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<section id="documentPreview" class="document-preview mt-3" hidden><div class="document-preview-header"><strong id="documentPreviewTitle">Preview</strong><button type="button" id="previewClose" class="btn btn-sm btn-outline-secondary">Tutup</button></div><iframe id="documentPreviewFrame" title="Preview dokumen"></iframe><div id="spreadsheetPreview" class="spreadsheet-preview" hidden><span class="text-muted">Memuat preview Excel...</span></div></section>

	@if($canManage)<form method="POST" action="{{ route('iso-documents.destroy', $isoDocument) }}" class="mt-3" onsubmit="return confirm('Hapus folder dokumen ISO ini beserta semua file di dalamnya?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Hapus Folder</button></form>@endif
</div>
<script>
	(() => {
		const preview = document.getElementById('documentPreview');
		const title = document.getElementById('documentPreviewTitle');
		const close = document.getElementById('previewClose');
		const frame = document.getElementById('documentPreviewFrame');
		const spreadsheet = document.getElementById('spreadsheetPreview');
		const setPreview = visible => { preview.hidden = !visible; if (!visible) { frame.src = ''; frame.hidden = false; spreadsheet.hidden = true; } };
		const loadSpreadsheet = async src => {
			spreadsheet.innerHTML = '<span class="text-muted">Memuat preview Excel...</span>';
			try {
				if (!window.XLSX) await new Promise((resolve, reject) => { const script = document.createElement('script'); script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js'; script.onload = resolve; script.onerror = reject; document.head.appendChild(script); });
				const response = await fetch(src, { credentials: 'same-origin' });
				if (!response.ok) throw new Error('Dokumen tidak dapat dimuat.');
				const workbook = window.XLSX.read(await response.arrayBuffer(), { type: 'array' });
				const rows = window.XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1, defval: '' });
				const table = document.createElement('table'); table.className = 'table table-sm table-bordered mb-0';
				rows.slice(0, 250).forEach((row, index) => { const tr = document.createElement('tr'); row.slice(0, 50).forEach(value => { const cell = document.createElement(index === 0 ? 'th' : 'td'); cell.textContent = value; tr.appendChild(cell); }); table.appendChild(tr); });
				spreadsheet.replaceChildren(table);
			} catch (error) { spreadsheet.textContent = 'Preview Excel tidak dapat dimuat. Silakan gunakan tombol unduh.'; }
		};
		document.querySelectorAll('.iso-preview-btn').forEach(button => {
			button.addEventListener('click', () => {
				const { src, name, type } = button.dataset;
				title.textContent = 'Preview: ' + name;
				setPreview(true);
				if (type === 'pdf') { frame.hidden = false; spreadsheet.hidden = true; frame.src = src; }
				else { frame.hidden = true; spreadsheet.hidden = false; loadSpreadsheet(src); }
			});
		});
		close.addEventListener('click', () => setPreview(false));
	})();
</script>
<style>
.iso-file-name{display:flex;align-items:center;gap:8px}
.iso-file-name i{font-size:1.1rem;color:#64748b}
.iso-file-name i.file-icon-pdf{color:#dc2626}
.iso-file-name i.file-icon-xls,.iso-file-name i.file-icon-xlsx{color:#15803d}
.iso-file-name i.file-icon-doc,.iso-file-name i.file-icon-docx{color:#1d4ed8}
.iso-file-name i.file-icon-ppt,.iso-file-name i.file-icon-pptx{color:#c2410c}
.iso-folder-card .card-header{background:#f8fafc}
.document-preview{border:1px solid #cbd5e1;border-radius:6px;overflow:hidden;background:#fff}
.document-preview-header{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-bottom:1px solid #dbe5ef}
.document-preview iframe{display:block;width:100%;height:min(75vh,900px);border:0;background:#f1f5f9}
.spreadsheet-preview{max-height:75vh;overflow:auto;background:#fff}
.spreadsheet-preview table{font-size:.8rem;white-space:nowrap}
.spreadsheet-preview th{background:#e8f1fa;position:sticky;top:0}
@media(max-width:576px){.document-preview iframe{height:65vh}.spreadsheet-preview{max-height:65vh}}
</style>
@endsection
