@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><strong>{{ $equipment->name }}</strong></div>
        <div class="card-body">
            <p><strong>Tipe:</strong> <span class="badge badge-type">{{ $equipment->type->name ?? '-' }}</span></p>
            <p><strong>Manufacturer:</strong> {{ $equipment->manufacturer->name ?? '-' }}</p>
            <p><strong>Lokasi:</strong> {{ $equipment->location?->name ?? $equipment->location }}</p>
                <p><strong>No. Seri:</strong> {{ $equipment->serial_number }}</p>
                <p><strong>Kapasitas:</strong> {{ $equipment->capacity }}</p>
                <p><strong>Spesifikasi:</strong><br/> {!! nl2br(e($equipment->specification)) !!}</p>
                <p><strong>Tahun pembuatan / pembelian:</strong> {{ $equipment->manufacture_year ?? ($equipment->purchase_date?->format('Y') ?? '-') }}</p>
            <p><strong>Lokasi:</strong> {{ $equipment->location }}</p>
                <p><strong>Kondisi:</strong> {{ $equipment->condition ?? $equipment->status }}</p>
                <p><strong>IP Address:</strong> {{ $equipment->ip_address }}</p>

    <h3 class="mt-3">Riwayat Perawatan</h3>
    <a href="{{ route('maintenances.checklists') }}" class="btn btn-brand btn-sm">Daftar Checklist</a>
    <table class="table mt-2">
        <thead><tr><th>Tanggal</th><th>Item</th><th>Hasil</th><th>Catatan</th></tr></thead>
        <tbody>
        @foreach($equipment->logs as $log)
            <tr>
                <td>{{ $log->performed_at }}</td>
                <td>{{ $log->checklistItem->title ?? '-' }}</td>
                <td>{{ $log->result }}</td>
                <td>{{ $log->remarks }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
        </div>
    </div>
</div>
@endsection
