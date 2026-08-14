@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Manufacturers</div>
        <div class="card-body">
            <a href="{{ route('masters.manufacturers.create') }}" class="btn btn-brand mb-3">Tambah</a>
            <table class="table">
                <thead><tr><th>Nama</th><th>Country</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($items as $it)
                    <tr>
                        <td>{{ $it->name }}</td>
                        <td>{{ $it->country }}</td>
                        <td>
                            <a href="{{ route('masters.manufacturers.edit', $it) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('masters.manufacturers.destroy', $it) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="table-pagination">{{ $items->links() }}</div>
        </div>
    </div>
</div>
@endsection
