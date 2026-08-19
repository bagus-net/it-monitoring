<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') · IT Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/it-theme.css') }}" rel="stylesheet">
    <style>
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0b5ea8,#08386b);padding:24px}
        .auth-card{width:100%;max-width:420px;background:#fff;border-radius:8px;box-shadow:0 18px 40px rgba(8,25,50,.28);overflow:hidden}
        .auth-head{padding:22px 26px;background:#f8fafc;border-bottom:1px solid #dbe5ef}
        .auth-head span{display:block;color:#0b5ea8;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
        .auth-head h1{margin:2px 0 0;font-size:1.32rem;color:#17324d}
        .auth-body{padding:22px 26px}
        .auth-body .form-label{font-size:.78rem;font-weight:700;color:#475569}
        .auth-foot{padding:0 26px 22px;font-size:.82rem;color:#64748b}
        .auth-foot a{color:#0b5ea8;font-weight:700;text-decoration:none}
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-head">
            <span>IT Monitoring System</span>
            <h1>@yield('heading')</h1>
        </div>
        <div class="auth-body">
            @if(session('status'))<div class="alert alert-success py-2">{{ session('status') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-danger py-2"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('form')
        </div>
        <div class="auth-foot">@yield('footer')</div>
    </div>
</body>
</html>
