<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Masuk') · IT Monitoring</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mgm.svg') }}">
    <style>
        #pageLoader{position:fixed;inset:0;z-index:2147483647;display:grid;place-items:center;background:radial-gradient(circle at 50% 42%,#fff 0,#f4f9fd 45%,#e6f0f7 100%);transition:opacity .24s ease,visibility .24s ease}
        #pageLoader.is-hidden{opacity:0;visibility:hidden;pointer-events:none}
        #pageLoaderCard{display:flex;flex-direction:column;align-items:center;gap:12px;min-width:176px;padding:24px 26px 21px;border:1px solid rgba(255,255,255,.9);border-radius:18px;background:rgba(255,255,255,.82);box-shadow:0 18px 45px rgba(20,76,112,.14),inset 0 1px 0 #fff;color:#17324d}
        #pageLoaderMark{position:relative;width:42px;height:42px;border:3px solid #d8e7f1;border-top-color:#0b5ea8;border-right-color:#14a79a;border-radius:50%;animation:pageLoaderSpin .8s linear infinite}
        #pageLoaderMark::after{position:absolute;inset:9px;border-radius:50%;background:linear-gradient(135deg,#0b5ea8,#14a79a);box-shadow:0 0 0 5px rgba(11,94,168,.08);content:""}
        #pageLoaderTitle{font-size:.76rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
        #pageLoaderStatus{display:flex;align-items:center;gap:4px;color:#7890a4;font-size:.68rem;font-weight:600}
        #pageLoaderStatus i{width:4px;height:4px;border-radius:50%;background:#14a79a;animation:pageLoaderDot 1s ease-in-out infinite}
        #pageLoaderStatus i:nth-child(2){animation-delay:.15s}#pageLoaderStatus i:nth-child(3){animation-delay:.3s}
        @keyframes pageLoaderSpin{to{transform:rotate(360deg)}}
        @keyframes pageLoaderDot{0%,60%,100%{opacity:.3;transform:translateY(0)}30%{opacity:1;transform:translateY(-2px)}}
    </style>
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
    <div id="pageLoader" role="status" aria-label="Memuat halaman"><div id="pageLoaderCard"><span id="pageLoaderMark" aria-hidden="true"></span><strong id="pageLoaderTitle">IT Monitoring</strong><span id="pageLoaderStatus">Menyiapkan halaman <i></i><i></i><i></i></span></div></div>
    <script>
        window.addEventListener('load', function () {
            const pageLoader = document.getElementById('pageLoader');
            pageLoader?.classList.add('is-hidden');
            window.setTimeout(() => pageLoader?.remove(), 220);
        });
        window.setTimeout(function () {
            const pageLoader = document.getElementById('pageLoader');
            pageLoader?.classList.add('is-hidden');
            window.setTimeout(() => pageLoader?.remove(), 220);
        }, 4000);
    </script>
    <div class="auth-shell">
        <section class="auth-brand">
            <div class="brand-content"><div class="brand-lockup"><span class="brand-logo"><img src="{{ asset('images/logo-mgm.svg') }}" width="48" height="48" alt="Logo PT Mulia Grand Manufacture"></span><span>PT Mulia Grand Manufacture<small>IT Monitoring &amp; Maintenance</small></span></div><div class="brand-message"><span>SISTEM OPERASIONAL INTERNAL</span><h1>Kelola layanan IT dalam satu ruang kerja.</h1><p>Gunakan akun perusahaan Anda untuk mengakses data peralatan, perawatan, dokumen, dan aktivitas operasional sesuai hak akses.</p></div></div>
            <div class="brand-footer">Dibuat oleh ITMGM 2026</div>
        </section>
        <main class="auth-main"><div class="auth-card"><div class="auth-head"><span>IT Monitoring System</span><h2>@yield('heading')</h2><p>Masukkan kredensial akun Anda untuk melanjutkan.</p></div><div class="auth-body">@if(session('status'))<div class="alert alert-success py-2">{{ session('status') }}</div>@endif @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('form')</div><div class="auth-foot">@yield('footer')</div></div></main>
    </div>
</body>
</html>
