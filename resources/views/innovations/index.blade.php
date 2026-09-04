@extends('layouts.app')

@section('content')
<div class="container mt-4 innovation-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div class="d-flex align-items-start gap-3">
            <span class="workflow-page-icon innovation-icon"><i class="bi bi-lightbulb"></i></span>
            <div>
                <div class="workflow-eyebrow">IT Improvement Lab</div>
                <h2 class="mb-1">Inovasi IT</h2>
                <p class="text-muted mb-0">Dokumentasi inovasi dan implementasi yang dilakukan tim IT.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('innovations.print', ['year' => $year ?: now()->year]) }}" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </a>
            <a href="{{ route('innovations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Inovasi
            </a>
        </div>
    </div>
    <form method="GET" class="row g-2 align-items-end mb-3"><div class="col-md-3"><label for="year" class="form-label">Tahun</label><select id="year" name="year" class="form-select"><option value="">Semua Tahun</option>@foreach ($availableYears as $availableYear)<option value="{{ $availableYear }}" {{ $year === (int) $availableYear ? 'selected' : '' }}>{{ $availableYear }}</option>@endforeach</select></div><div class="col-md-5"><label for="search" class="form-label">Cari Inovasi</label><input id="search" name="search" class="form-control" value="{{ $search }}" placeholder="Judul, implementasi, atau keterangan"></div><div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">Filter</button><a href="{{ route('innovations.index') }}" class="btn btn-outline-secondary">Reset</a></div></form>
    <div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Tanggal</th><th>Inovasi</th><th>Implementasi</th><th>Tgl Implementasi</th><th>Paper</th><th></th></tr></thead><tbody>@forelse ($innovations as $innovation)<tr><td>{{ $innovation->innovation_date->format('d M Y') }}</td><td><strong>{{ $innovation->title }}</strong><small class="d-block text-muted">{{ $innovation->creator->name ?? '-' }}</small></td><td>{{ \Illuminate\Support\Str::limit($innovation->implementation, 80) ?: '-' }}</td><td>{{ $innovation->implementation_date?->format('d M Y') ?? '-' }}</td><td>@if ($innovation->paper_path)<a href="{{ asset('storage/' . $innovation->paper_path) }}" target="_blank" class="btn btn-sm btn-outline-danger">Buka Paper</a>@else<span class="text-muted">-</span>@endif</td><td class="text-nowrap"><a href="{{ route('innovations.show', $innovation) }}" class="btn btn-sm btn-outline-secondary">Detail</a><a href="{{ route('innovations.edit', $innovation) }}" class="btn btn-sm btn-outline-primary">Edit</a><form method="POST" action="{{ route('innovations.destroy', $innovation) }}" class="d-inline" onsubmit="return confirm('Hapus inovasi ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">Belum ada inovasi IT yang dicatat.</td></tr>@endforelse</tbody></table></div></div>
    <div class="table-pagination">{{ $innovations->links() }}</div>
</div>
@endsection
