@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-colorful">
        <div class="card-header">Peralatan IT</div>
        <div class="card-body">
            <a href="{{ route('equipments.create') }}" class="btn btn-brand mb-3">Tambah Peralatan</a>
            <div class="table-responsive">
            <table class="table mt-3">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Merk</th>
                <th>No. Seri</th>
                <th>Kapasitas / Spesifikasi</th>
                <th>Tahun</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>IP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($equipments as $eq)
            <tr>
                <td><strong>{{ $eq->name }}</strong></td>
                <td>{{ $eq->manufacturer->name ?? ($eq->type->name ?? '-') }}</td>
                <td>{{ $eq->serial_number }}</td>
                <td>{{ $eq->capacity }}</td>
                <td>{{ $eq->manufacture_year ?? ($eq->purchase_date?->format('Y') ?? '-') }}</td>
                <td>{{ $eq->location->name ?? '-' }}</td>
                <td>{{ $eq->condition ?? $eq->status }}</td>
                <td>{{ $eq->ip_address }}</td>
                <td>
                    <a href="{{ route('equipments.show', $eq) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    <a href="{{ route('equipments.edit', $eq) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('equipments.destroy', $eq) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus peralatan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
