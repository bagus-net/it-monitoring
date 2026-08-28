@extends('layouts.app')

@section('content')
@php($extension = strtolower(pathinfo($isoDocument->file_name, PATHINFO_EXTENSION)))
@php($isPdf = $extension === 'pdf')
@php($isSpreadsheet = in_array($extension, ['xls', 'xlsx'], true))
@php($isPreviewable = $isPdf || $isSpreadsheet)
<div class="container mt-4 iso-document-detail">
	<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
		<div><div class="text-primary small fw-bold">{{ $isoDocument->document_number }}</div><h2 class="mb-1">{{ $isoDocument->title }}</h2><p class="text-muted mb-0">{{ $isoDocument->category }} · {{ $isoDocument->document_date?->format('d F Y') ?? 'Tanggal belum dicatat' }}</p></div>
		<div class="d-flex flex-wrap gap-2">
			@if($isPreviewable)<button type="button" id="previewToggle" class="btn btn-outline-primary" aria-expanded="false" aria-controls="documentPreview"><i class="bi bi-eye"></i> Preview Dokumen</button>@endif
			<a href="{{ route('iso-documents.download', $isoDocument) }}" class="btn btn-primary">Unduh Dokumen</a>
			@if(auth()->user()->isMaster() || auth()->user()->isAdminIt())<a href="{{ route('iso-documents.edit', $isoDocument) }}" class="btn btn-outline-primary">Edit</a>@endif
			<a href="{{ route('iso-documents.index') }}" class="btn btn-outline-secondary">Kembali</a>
		</div>
	</div>
	<div class="card"><div class="card-body"><dl class="row mb-0"><dt class="col-md-3">Nama File</dt><dd class="col-md-9">{{ $isoDocument->file_name }}</dd><dt class="col-md-3">Revisi</dt><dd class="col-md-9">{{ $isoDocument->revision ?: '-' }}</dd><dt class="col-md-3">Deskripsi</dt><dd class="col-md-9">{!! nl2br(e($isoDocument->description ?: '-')) !!}</dd><dt class="col-md-3">Dibagikan Oleh</dt><dd class="col-md-9">{{ $isoDocument->creator->name ?? '-' }}</dd>@if(auth()->user()->isMaster() || auth()->user()->isAdminIt())<dt class="col-md-3">Pengguna Berizin</dt><dd class="col-md-9">{{ $isoDocument->permittedUsers->pluck('name')->join(', ') }}</dd>@endif</dl></div></div>
	@if($isPreviewable)<section id="documentPreview" class="document-preview mt-3" hidden><div class="document-preview-header"><strong>Preview: {{ $isoDocument->file_name }}</strong><button type="button" id="previewClose" class="btn btn-sm btn-outline-secondary">Tutup</button></div>@if($isPdf)<iframe id="documentPreviewFrame" title="Preview {{ $isoDocument->file_name }}" data-src="{{ route('iso-documents.preview', $isoDocument) }}"></iframe>@else<div id="spreadsheetPreview" class="spreadsheet-preview" data-src="{{ route('iso-documents.preview', $isoDocument) }}"><span class="text-muted">Memuat preview Excel...</span></div>@endif</section>@endif
	@if(auth()->user()->isMaster() || auth()->user()->isAdminIt())<form method="POST" action="{{ route('iso-documents.destroy', $isoDocument) }}" class="mt-3" onsubmit="return confirm('Hapus dokumen ISO ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Hapus Dokumen</button></form>@endif
</div>
@if($isPreviewable)<script>
	(() => {
		const preview = document.getElementById('documentPreview');
		const toggle = document.getElementById('previewToggle');
		const close = document.getElementById('previewClose');
		const frame = document.getElementById('documentPreviewFrame');
		const spreadsheet = document.getElementById('spreadsheetPreview');
		let spreadsheetLoaded = false;
		const loadSpreadsheet = async () => {
			if (spreadsheetLoaded || !spreadsheet) return;
			try {
				if (!window.XLSX) await new Promise((resolve, reject) => { const script = document.createElement('script'); script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js'; script.onload = resolve; script.onerror = reject; document.head.appendChild(script); });
				const response = await fetch(spreadsheet.dataset.src, { credentials: 'same-origin' });
				if (!response.ok) throw new Error('Dokumen tidak dapat dimuat.');
				const workbook = window.XLSX.read(await response.arrayBuffer(), { type: 'array' });
				const rows = window.XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1, defval: '' });
				const table = document.createElement('table'); table.className = 'table table-sm table-bordered mb-0';
				rows.slice(0, 250).forEach((row, index) => { const tr = document.createElement('tr'); row.slice(0, 50).forEach(value => { const cell = document.createElement(index === 0 ? 'th' : 'td'); cell.textContent = value; tr.appendChild(cell); }); table.appendChild(tr); });
				spreadsheet.replaceChildren(table); spreadsheetLoaded = true;
			} catch (error) { spreadsheet.textContent = 'Preview Excel tidak dapat dimuat. Silakan gunakan Unduh Dokumen.'; }
		};
		const setPreview = visible => {
			preview.hidden = !visible;
			toggle.setAttribute('aria-expanded', String(visible));
			toggle.innerHTML = visible ? '<i class="bi bi-eye-slash"></i> Tutup Preview' : '<i class="bi bi-eye"></i> Preview Dokumen';
			if (visible && frame && !frame.src) frame.src = frame.dataset.src;
			if (visible) loadSpreadsheet();
		};
		toggle.addEventListener('click', () => setPreview(preview.hidden));
		close.addEventListener('click', () => setPreview(false));
	})();
</script>
<style>.document-preview{border:1px solid #cbd5e1;border-radius:6px;overflow:hidden;background:#fff}.document-preview-header{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-bottom:1px solid #dbe5ef}.document-preview iframe{display:block;width:100%;height:min(75vh,900px);border:0;background:#f1f5f9}.spreadsheet-preview{max-height:75vh;overflow:auto;background:#fff}.spreadsheet-preview table{font-size:.8rem;white-space:nowrap}.spreadsheet-preview th{background:#e8f1fa;position:sticky;top:0}@media(max-width:576px){.document-preview iframe{height:65vh}.spreadsheet-preview{max-height:65vh}}</style>@endif
@endsection
