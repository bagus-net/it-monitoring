@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Checklist Perawatan</div>
        <div class="card-body">
            <a href="#" class="btn btn-brand mb-3">Tambah Item</a>
            <div class="table-responsive">
            <table class="table mt-3">
                <thead><tr><th>Urut</th><th>Judul</th><th>Tipe Peralatan</th></tr></thead>
                <tbody>
                @foreach($items as $it)
                    <tr>
                        <td>{{ $it->sort_order }}</td>
                        <td>{{ $it->title }}</td>
                        <td><span class="badge badge-type">{{ $it->equipmentType->name ?? '-' }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection
