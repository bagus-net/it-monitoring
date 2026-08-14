@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color: #ff9800;">📋 Jadwal Bulanan</h2>
                <a href="{{ route('monthly_schedules.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Jadwal Baru
                </a>
            </div>

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($groups->count())
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-warning">
                            <tr>
                                <th>Program Perawatan</th>
                                <th>Tahun</th>
                                <th>Jumlah Peralatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td><span class="program-dot" style="background-color:{{ $group['checklist_item']->schedule_color }}"></span><strong>{{ $group['checklist_item']->title ?? 'N/A' }}</strong></td>
                                    <td>{{ $group['year'] }}</td>
                                    <td>{{ $group['equipment_count'] }}</td>
                                    <td>
                                        <a href="{{ route('monthly_schedules.show', [$group['checklist_item_id'], $group['year']]) }}"
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('monthly_schedules.select_months', [$group['checklist_item_id'], $group['year']]) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('monthly_schedules.destroy', [$group['checklist_item_id'], $group['year']]) }}"
                                              style="display:inline;"
                                              onsubmit="return confirm('Yakin ingin menghapus seluruh jadwal bulanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada jadwal bulanan. <a href="{{ route('monthly_schedules.create') }}">Buat jadwal baru</a>
                </div>
            @endif
        </div>
    </div>
</div>
<style>.program-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:7px; }</style>
@endsection
