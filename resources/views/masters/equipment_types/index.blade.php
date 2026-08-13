@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Tipe Peralatan</div>
        <div class="card-body">
            <a href="{{ route('masters.equipment-types.create') }}" class="btn btn-brand mb-3">Tambah Tipe</a>
            <table class="table">
                <thead><tr><th>Nama</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($items as $it)
                    <tr>
                        <td>{{ $it->name }}</td>
                        <td>{{ $it->description }}</td>
                        <td>
                            <a href="{{ route('masters.equipment-types.edit', $it) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('masters.equipment-types.destroy', $it) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
