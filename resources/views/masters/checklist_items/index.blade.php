@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-colorful">
        <div class="card-header">Program Perawatan</div>
        <div class="card-body">
            <a href="{{ route('masters.checklist-items.create') }}" class="btn btn-brand mb-3">Tambah Program</a>
            <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Frequency</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($items as $it)
                    <tr>
                        <td>{{ $it->title }}</td>
                        <td>{{ $it->category ?? '-' }}</td>
                        <td>{{ $it->equipmentType->name ?? '-' }}</td>
                        <td>{{ $it->frequency ?? '-' }}</td>
                        <td>
                            <a href="{{ route('masters.checklist-items.edit', $it) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('masters.checklist-items.destroy', $it) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
            <div class="table-pagination">{{ $items->links() }}</div>
        </div>
    </div>
</div>
@endsection
