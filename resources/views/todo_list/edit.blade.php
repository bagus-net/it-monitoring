@extends('layouts.app')
@section('content')
<div class="container todo-page todo-form-page"><div class="todo-heading"><div><div class="todo-eyebrow">WORK MANAGEMENT</div><h1>Edit To-do</h1><p>Perbarui status, PIC, prioritas, dan deadline task.</p></div><a href="{{ route('todo-list.index') }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('todo-list.update', $task) }}" class="card todo-form-card">@csrf @method('PUT')<div class="card-header"><i class="bi bi-pencil-square"></i><strong>Task Workspace</strong></div><div class="card-body">@include('todo_list.form')</div><div class="card-footer text-end"><button class="btn btn-brand"><i class="bi bi-check2-circle"></i>Simpan Perubahan</button></div></form></div>
@endsection
