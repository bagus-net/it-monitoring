@extends('layouts.app')
@section('content')
<div class="container todo-page todo-form-page"><div class="todo-heading"><div><div class="todo-eyebrow">WORK MANAGEMENT</div><h1>Tambah To-do</h1><p>Buat pekerjaan yang dapat dilacak lintas campaign dan PIC.</p></div><a href="{{ route('todo-list.index') }}" class="btn btn-outline-secondary">Kembali</a></div><form method="POST" action="{{ route('todo-list.store') }}" class="card todo-form-card">@csrf<div class="card-header"><i class="bi bi-check2-square"></i><strong>Task Workspace</strong></div><div class="card-body">@include('todo_list.form', ['task' => null])</div><div class="card-footer text-end"><button class="btn btn-brand"><i class="bi bi-check2-circle"></i>Simpan To-do</button></div></form></div>
@endsection
