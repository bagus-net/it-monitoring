<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/it-theme.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg" style="background:linear-gradient(90deg,#0ea5a2,#6a5cff)">
        <div class="container">
            <a class="navbar-brand nav-brand" href="{{ route('dashboard') }}">IT Monitoring</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('dashboard') }}">Web Monitoring</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('equipments.index') }}">Peralatan</a></li>

                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('maintenances.schedules') }}">Jadwal</a></li>
                    {{-- <li class="nav-item"><a class="nav-link text-white" href="{{ route('maintenances.create') }}">Tambah Jadwal</a></li> --}}
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('maintenances.checklists') }}">Checklist</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="{{ route('maintenances.grid') }}">Grid</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">Masters</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('masters.manufacturers.index') }}">Manufacturers</a></li>
                            <li><a class="dropdown-item" href="{{ route('masters.locations.index') }}">Locations</a></li>
                            <li><a class="dropdown-item" href="{{ route('masters.equipment-types.index') }}">Tipe Peralatan</a></li>
                            <li><a class="dropdown-item" href="{{ route('masters.checklist-items.index') }}">Program Perawatan</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="hero mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="m-0">IT Monitoring</h2>
                    <div style="opacity:0.9">Dashboard & Perawatan Peralatan</div>
                </div>
                <div>
                    <a class="btn btn-light" href="#">Bantuan</a>
                </div>
            </div>
        </div>
    </div>
    <main class="py-4">
        @if(session('success'))
            <div class="container"><div class="alert alert-success">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="container"><div class="alert alert-danger">{{ session('error') }}</div></div>
        @endif
        @yield('content')
    </main>
</body>
</html>
