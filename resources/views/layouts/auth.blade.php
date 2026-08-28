<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') · IT Monitoring</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/it-theme.css') }}" rel="stylesheet">
    <style>
        :root{--auth-navy:#073b6d;--auth-blue:#0b5ea8;--auth-teal:#0f766e;--auth-ink:#17324d;--auth-muted:#64748b}
        body{min-height:100vh;margin:0;background:#e8f0f7;color:var(--auth-ink)}
        .auth-shell{display:grid;grid-template-columns:minmax(0,1.12fr) minmax(390px,.88fr);min-height:100vh}
        .auth-brand{position:relative;display:flex;flex-direction:column;justify-content:space-between;min-height:100%;padding:52px clamp(32px,6vw,88px);overflow:hidden;background:var(--auth-navy);color:#fff}
        .auth-brand:before,.auth-brand:after{content:'';position:absolute;inset:0;pointer-events:none}.auth-brand:before{background:repeating-linear-gradient(90deg,rgba(255,255,255,.055) 0 1px,transparent 1px 48px),repeating-linear-gradient(0deg,rgba(255,255,255,.04) 0 1px,transparent 1px 48px)}.auth-brand:after{top:auto;left:0;right:0;height:42%;background:#0b5ea8;clip-path:polygon(0 61%,100% 0,100% 100%,0 100%);opacity:.72}
        .brand-content,.brand-footer{position:relative;z-index:1}.brand-lockup{display:flex;align-items:center;gap:15px;font-weight:800;font-size:1.05rem}.brand-logo{display:flex;align-items:center;justify-content:center;width:58px;height:58px;padding:5px;background:#fff;border-radius:8px}.brand-logo img{width:100%;height:100%;object-fit:contain}.brand-lockup small{display:block;margin-top:4px;color:#b9d4ed;font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.brand-message{max-width:610px;margin:90px 0 40px}.brand-message span{display:block;margin-bottom:12px;color:#7dd3fc;font-size:.72rem;font-weight:800;letter-spacing:.14em}.brand-message h1{max-width:580px;margin:0;font-size:clamp(2rem,4vw,3.5rem);line-height:1.14;letter-spacing:0}.brand-message p{max-width:520px;margin:18px 0 0;color:#dbeafe;font-size:1rem;line-height:1.7}.brand-footer{font-size:.8rem;color:#bfdbfe}.auth-main{display:flex;align-items:center;justify-content:center;padding:36px;background:#f8fbfe}.auth-card{width:100%;max-width:440px;background:#fff;border:1px solid #dbe5ef;border-radius:8px;box-shadow:0 18px 40px rgba(15,45,75,.13);overflow:hidden}.auth-head{padding:28px 30px 18px}.auth-head span{display:block;color:var(--auth-blue);font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.auth-head h2{margin:5px 0 0;font-size:1.55rem;color:var(--auth-ink)}.auth-head p{margin:8px 0 0;color:var(--auth-muted);font-size:.88rem}.auth-body{padding:4px 30px 24px}.auth-body .form-label{font-size:.78rem;font-weight:700;color:#475569}.auth-body .form-control{padding:10px 12px;border-color:#cbd5e1}.auth-body .form-control:focus{border-color:var(--auth-blue);box-shadow:0 0 0 .2rem rgba(11,94,168,.14)}.auth-body .btn-brand{padding:10px 16px;background:var(--auth-blue);font-weight:700}.auth-body .btn-brand:hover{background:#084e8d}.auth-foot{padding:0 30px 28px;color:var(--auth-muted);font-size:.82rem;line-height:1.5}.auth-foot a{color:var(--auth-blue);font-weight:700;text-decoration:none}@media(max-width:850px){.auth-shell{display:block}.auth-brand{min-height:auto;padding:30px 28px}.brand-message{display:none}.brand-footer{margin-top:28px}.auth-main{min-height:calc(100vh - 145px);padding:28px 20px}}@media(max-width:460px){.auth-brand{padding:22px}.brand-logo{width:46px;height:46px}.auth-main{padding:18px 14px}.auth-head,.auth-body{padding-left:22px;padding-right:22px}.auth-foot{padding:0 22px 22px}}
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-brand">
            <div class="brand-content"><div class="brand-lockup"><span class="brand-logo"><img src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture"></span><span>PT Mulia Grand Manufacture<small>IT Monitoring &amp; Maintenance</small></span></div><div class="brand-message"><span>SISTEM OPERASIONAL INTERNAL</span><h1>Kelola layanan IT dalam satu ruang kerja.</h1><p>Gunakan akun perusahaan Anda untuk mengakses data peralatan, perawatan, dokumen, dan aktivitas operasional sesuai hak akses.</p></div></div>
            <div class="brand-footer">Dibuat oleh ITMGM 2026</div>
        </section>
        <main class="auth-main"><div class="auth-card"><div class="auth-head"><span>IT Monitoring System</span><h2>@yield('heading')</h2><p>Masukkan kredensial akun Anda untuk melanjutkan.</p></div><div class="auth-body">@if(session('status'))<div class="alert alert-success py-2">{{ session('status') }}</div>@endif @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('form')</div><div class="auth-foot">@yield('footer')</div></div></main>
    </div>
</body>
</html>
