@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-colorful">
        <div class="card-header">Jadwal Perawatan</div>
        <div class="card-body">
            <a href="{{ route('maintenances.create') }}" class="btn btn-brand mb-3">Tambah Jadwal</a>
            <div class="table-responsive">
            <table class="table mt-3">
                <thead><tr><th>Program Perawatan</th><th>Frekuensi</th><th>Peralatan</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($groups as $g)
                    <tr>
                        <td>{{ $g['item']->title }}</td>
                        <td>{{ implode(', ', $g['frequencies']) }}</td>
                        <td>
                            @if(in_array(null, $g['equipment_ids'])) Semua @else {{ count(array_filter($g['equipment_ids'])) }} items @endif
                        </td>
                        <td>
                            <a href="{{ route('maintenances.schedules.show', $g['item']->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            <a href="{{ route('maintenances.schedules.edit', $g['item']->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('maintenances.schedules.destroy', $g['item']->id) }}" style="display:inline" onsubmit="return confirm('Hapus semua jadwal untuk program ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada jadwal.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
