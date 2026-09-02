<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT Monitoring</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mgm.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/css/it-theme.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <button class="sidebar-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Buka navigasi">Menu</button>
    <aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="appSidebar">
        <div class="offcanvas-header sidebar-mobile-header">
            <strong>PT Mulia Grand Manufacture</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-block">
            @php($currentUser = auth()->user())
            <a class="sidebar-brand" href="{{ $currentUser && $currentUser->isEmployee() ? route('it-repair-tickets.index') : route('dashboard') }}"><span class="sidebar-brand-mark"><img src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture"></span><span>PT Mulia Grand Manufacture<small>IT Monitoring &amp; Maintenance</small></span></a>
            <nav class="sidebar-nav">
                @if($currentUser && !$currentUser->isEmployee())
                <a @class(['sidebar-link','active'=>request()->routeIs('dashboard')]) href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i>Dashboard Utama</a>
                @if($currentUser->isMaster())
                <a @class(['sidebar-link','active'=>request()->routeIs('campaigns.*')]) href="{{ route('campaigns.index') }}"><i class="bi bi-megaphone"></i>Campaign</a>
                <a @class(['sidebar-link','active'=>request()->routeIs('todo-list.*')]) href="{{ route('todo-list.index') }}"><i class="bi bi-check2-square"></i>To-do List</a>
                @endif
                <a @class(['sidebar-link','active'=>request()->routeIs('web-monitoring.*')]) href="{{ route('web-monitoring.index') }}"><i class="bi bi-globe2"></i>Web Monitoring</a>
                @endif
                <span class="sidebar-label">Operasional</span>

                @if($currentUser && !$currentUser->isEmployee())


                <a @class(['sidebar-link','active'=>request()->routeIs('equipments.*')]) href="{{ route('equipments.index') }}"><i class="bi bi-laptop"></i>Peralatan IT / Asset</a>
                <a @class(['sidebar-link sidebar-ticket-link','active'=>request()->routeIs('equipment-transfers.*')]) href="{{ route('equipment-transfers.index') }}"><i class="bi bi-arrow-left-right"></i><span>Mutasi Peralatan</span><span class="ticket-badge-group"><span id="transferPendingApprovalBadge" class="ticket-notification-badge d-none" title="Mutasi baru menunggu approve">0</span><span id="transferMyUnfinishedBadge" class="ticket-notification-badge badge-progress d-none" title="Mutasi saya belum selesai">0</span></span></a>
                @endif
                <a @class(['sidebar-link sidebar-ticket-link','active'=>request()->routeIs('it-repair-tickets.*')]) href="{{ route('it-repair-tickets.index') }}"><i class="bi bi-tools"></i><span>Perbaikan IT / Ticketing</span><span class="ticket-badge-group"><span id="ticketNotificationBadge" class="ticket-notification-badge d-none" title="Tiket open">0</span><span id="ticketProgressBadge" class="ticket-notification-badge badge-progress d-none" title="Tiket sedang dikerjakan">0</span></span></a>

                @if($currentUser && !$currentUser->isEmployee())
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="bi bi-clipboard2-check"></i>Checklist</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('web-monitoring-checklists.index') }}"><i class="bi bi-globe2"></i>Web Monitoring</a></li><li><a class="dropdown-item" href="{{ route('maintenance-checklists.index') }}"><i class="bi bi-clipboard-check"></i>Peralatan IT</a></li></ul></div>
                {{-- <a class="sidebar-link" href="{{ route('maintenances.grid') }}">Grid Perawatan</a> --}}

                <span class="sidebar-label">Perencanaan</span>
                @if($currentUser->isMaster())
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="bi bi-calendar3"></i>Jadwal</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('maintenances.schedules') }}"><i class="bi bi-calendar-range"></i>Jadwal Tahunan</a></li><li><a class="dropdown-item" href="{{ route('monthly_schedules.index') }}"><i class="bi bi-calendar-week"></i>Jadwal Bulanan</a></li></ul></div>
                @endif
                <a @class(['sidebar-link','active'=>request()->routeIs('innovations.*')]) href="{{ route('innovations.index') }}"><i class="bi bi-lightbulb"></i>Inovasi IT</a>
                <a @class(['sidebar-link','active'=>request()->routeIs('target-monitorings.*')]) href="{{ route('target-monitorings.index') }}"><i class="bi bi-bullseye"></i>Pemantauan Sasaran</a>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="bi bi-bar-chart-line"></i>Laporan</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('reports.annual') }}"><i class="bi bi-file-earmark-bar-graph"></i>Laporan Tahunan</a></li><li><a class="dropdown-item" href="{{ route('reports.monthly') }}"><i class="bi bi-file-earmark-bar-graph"></i>Laporan Bulanan</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="{{ route('reports.equipments') }}"><i class="bi bi-file-earmark-text"></i>Laporan Peralatan IT</a></li><li><a class="dropdown-item" href="{{ route('reports.repairs') }}"><i class="bi bi-file-earmark-text"></i>Laporan Perbaikan IT</a></li><li><a class="dropdown-item" href="{{ route('reports.checklists') }}"><i class="bi bi-file-earmark-text"></i>Laporan Checklist Web &amp; Peralatan IT</a></li>@if($currentUser->isMaster())<li><a class="dropdown-item" href="{{ route('reports.activities') }}"><i class="bi bi-file-earmark-text"></i>Laporan Log Aktivitas</a></li>@endif</ul></div>
                <span class="sidebar-label">Master Data</span>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="bi bi-gear"></i>Pengaturan Master</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('masters.manufacturers.index') }}"><i class="bi bi-building"></i>Manufacturers</a></li><li><a class="dropdown-item" href="{{ route('masters.locations.index') }}"><i class="bi bi-geo-alt"></i>Lokasi</a></li><li><a class="dropdown-item" href="{{ route('masters.equipment-types.index') }}"><i class="bi bi-tags"></i>Tipe Peralatan</a></li><li><a class="dropdown-item" href="{{ route('masters.checklist-items.index') }}"><i class="bi bi-list-check"></i>Program Perawatan</a></li></ul></div>
                @endif
                @if($currentUser && !$currentUser->isEmployee())
                <span class="sidebar-label">Inventaris &amp; Persediaan</span>
                <div class="sidebar-dropdown dropdown sidebar-manage-dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown"><span class="sidebar-ink-icon">KG</span><span>Kelola</span></button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('ink.index') }}"><span class="sidebar-ink-icon">IN</span>Kelola Tinta</a></li><li><a class="dropdown-item" href="{{ route('it-wastes.index') }}"><span class="sidebar-ink-icon">LB</span>Limbah IT</a></li><li><a class="dropdown-item" href="{{ route('spareparts.index') }}"><span class="sidebar-ink-icon">SP</span>Kelola Sparepart</a></li><li><a class="dropdown-item" href="{{ route('licenses.index') }}"><span class="sidebar-ink-icon">LC</span>Kelola Lisensi</a></li><li><a class="dropdown-item" href="{{ route('cctv.index') }}"><span class="sidebar-ink-icon">TV</span>Kelola CCTV</a></li></ul></div>
                @endif
                @if($currentUser && !$currentUser->isEmployee())
                <span class="sidebar-label">Jaringan</span>
                <a @class(['sidebar-link sidebar-network-link','active'=>request()->routeIs('network.*')]) href="{{ route('network.topology') }}"><span class="sidebar-ink-icon">NT</span><span>Topologi Jaringan</span></a>
                @endif
                 <span class="sidebar-label">General</span>
                <a @class(['sidebar-link','active'=>request()->routeIs('iso-documents.*')]) href="{{ route('iso-documents.index') }}"><i class="bi bi-file-earmark-richtext"></i>Dokumen ISO</a>
                @if($currentUser && $currentUser->isMaster())
                <span class="sidebar-label">Pengaturan</span>
                <a @class(['sidebar-link','active'=>request()->routeIs('users.*')]) href="{{ route('users.index') }}"><i class="bi bi-people"></i>Pengaturan User</a>
                <a @class(['sidebar-link','active'=>request()->routeIs('activity-logs.*')]) href="{{ route('activity-logs.index') }}"><i class="bi bi-clock-history"></i>Log Aktivitas User</a>
                <a @class(['sidebar-link','active'=>request()->routeIs('recycle-bin.*')]) href="{{ route('recycle-bin.index') }}"><i class="bi bi-trash3"></i>Trash</a>
                @endif
            </nav>
            <div class="sidebar-footer">
                @auth
                <a class="sidebar-signature-link" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i>Profil Saya</a>
                <a class="sidebar-signature-link" href="{{ route('signature.edit') }}"><i class="bi bi-pen"></i>Tanda Tangan Digital</a>
                <a class="sidebar-signature-link" href="{{ route('privacy-policy') }}"><i class="bi bi-shield-check"></i>Privacy Policy</a>
                @endauth
                <button id="enableTicketAlerts" type="button" class="sidebar-alert-toggle"><i class="bi bi-bell"></i>Aktifkan notifikasi tiket</button>
                <span>IT Maintenance System</span>
                <span>Dibuat oleh ITMGM 2026</span>
            </div>
        </div>
    </aside>
    <main class="app-main py-4">
        <div class="app-topbar">
            <button type="button" id="sidebarCollapseToggle" class="sidebar-collapse-toggle" aria-label="Sembunyikan navigasi" aria-expanded="true" title="Sembunyikan navigasi"><i class="bi bi-layout-sidebar-inset"></i></button>
            <div class="topbar-search"><i class="bi bi-search"></i><input id="globalPageSearch" type="search" placeholder="Search..." aria-label="Cari halaman" autocomplete="off"><div id="globalSearchResults" class="global-search-results" hidden></div></div>
            <div class="topbar-spacer"></div>
            <div class="topbar-actions"><button type="button" id="topbarLanguageButton" class="topbar-icon-button" title="Bahasa" aria-label="Bahasa"><i class="bi bi-translate"></i></button><button type="button" id="topbarAppsButton" class="topbar-icon-button" title="Aplikasi" aria-label="Aplikasi"><i class="bi bi-grid-3x3-gap"></i></button><button type="button" id="topbarAlertButton" class="topbar-icon-button topbar-notification" title="Notifikasi tiket" aria-label="Notifikasi tiket"><i class="bi bi-bell"></i><span class="topbar-notification-dot"></span><span id="topbarNotificationCount" class="topbar-notification-count">0</span></button><button type="button" id="topbarSettingsButton" class="topbar-icon-button" title="Pengaturan" aria-label="Pengaturan"><i class="bi bi-gear"></i></button></div>
            @auth
            <a href="{{ route('profile.show') }}" class="topbar-user text-decoration-none"><span class="topbar-profile-photo"><img src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : asset('images/default-avatar.svg') }}" alt="Foto profil {{ auth()->user()->name }}"></span><span><span class="topbar-user-name">{{ auth()->user()->name }}</span><span class="topbar-user-role">{{ auth()->user()->roleLabel() }}</span></span></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="topbar-logout">Keluar</button></form>
            @endauth
        </div>
        <div id="topbarPopover" class="topbar-popover" hidden></div>
        <div class="print-letterhead">
            <img src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture">
            <div><strong>PT MULIA GRAND MANUFACTURE</strong><span>IT Monitoring &amp; Maintenance System</span></div>
            <div class="print-letterhead-meta">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
        </div>
        @if(session('success'))
            <div class="container-fluid app-content"><div class="alert alert-success">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="container-fluid app-content"><div class="alert alert-danger">{{ session('error') }}</div></div>
        @endif
        @yield('content')
        <footer class="app-footer">Dibuat oleh ITMGM 2026</footer>
    </main>
    @auth
    <nav class="mobile-bottom-nav" aria-label="Navigasi utama mobile">
        @if(!auth()->user()->isEmployee())
        <a @class(['mobile-bottom-link','active'=>request()->routeIs('dashboard')]) href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
        <a @class(['mobile-bottom-link','active'=>request()->routeIs('ink.*')]) href="{{ route('ink.index') }}"><i class="bi bi-droplet"></i><span>Tinta</span></a>
        <a @class(['mobile-bottom-link','active'=>request()->routeIs('equipments.*')]) href="{{ route('equipments.index') }}"><i class="bi bi-laptop"></i><span>Aset IT</span></a>
        @endif
        <a @class(['mobile-bottom-link','active'=>request()->routeIs('it-repair-tickets.*')]) href="{{ route('it-repair-tickets.index') }}"><i class="bi bi-tools"></i><span>Ticketing</span></a>
        @if(!auth()->user()->isEmployee())
        <a @class(['mobile-bottom-link','active'=>request()->routeIs('maintenance-checklists.*')]) href="{{ route('maintenance-checklists.index') }}"><i class="bi bi-clipboard2-check"></i><span>Checklist IT</span></a>
        @endif
        <button type="button" class="mobile-bottom-link" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-label="Buka semua menu"><i class="bi bi-list"></i><span>Menu</span></button>
    </nav>
    @endauth
    <div id="ticketToast" class="ticket-toast" role="status"><strong>Tiket baru masuk</strong><span id="ticketToastMessage"></span><a href="{{ route('it-repair-tickets.index') }}">Buka tiket</a></div>
    <div id="transferToast" class="ticket-toast" role="status"><strong>Mutasi baru menunggu approve</strong><span id="transferToastMessage"></span><a href="{{ route('equipment-transfers.index') }}">Buka mutasi</a></div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('globalPageSearch');
            const results = document.getElementById('globalSearchResults');
            if (!search || !results) return;
            const pages = [
                @if(auth()->user() && !auth()->user()->isEmployee())
                ['Dashboard Utama', 'dashboard', '{{ route('dashboard') }}', 'bi-grid-1x2-fill'],
                @if(auth()->user()->isMaster())
                ['Campaign', 'campaign marketing program target', '{{ route('campaigns.index') }}', 'bi-megaphone'],
                ['To-do List', 'todo task pekerjaan deadline', '{{ route('todo-list.index') }}', 'bi-check2-square'],
                @endif
                ['Web Monitoring', 'web monitoring situs website', '{{ route('web-monitoring.index') }}', 'bi-globe2'],
                ['Peralatan IT', 'asset equipment inventaris', '{{ route('equipments.index') }}', 'bi-laptop'],
                ['Mutasi Peralatan', 'transfer tukar pic', '{{ route('equipment-transfers.index') }}', 'bi-arrow-left-right'],
                @endif
                ['Perbaikan IT / Ticketing', 'ticket repair tiket', '{{ route('it-repair-tickets.index') }}', 'bi-tools'],
                @if(auth()->user() && !auth()->user()->isEmployee())
                ['Checklist', 'maintenance perawatan checklist', '{{ route('maintenance-checklists.index') }}', 'bi-clipboard2-check'],
                ['Jadwal Tahunan', 'schedule jadwal annual', '{{ route('maintenances.schedules') }}', 'bi-calendar3'],
                ['Jadwal Bulanan', 'monthly bulanan schedule', '{{ route('monthly_schedules.index') }}', 'bi-calendar-week'],
                ['Inovasi IT', 'innovation inovasi', '{{ route('innovations.index') }}', 'bi-lightbulb'],
                ['Pemantauan Sasaran', 'target sasaran performance', '{{ route('target-monitorings.index') }}', 'bi-bullseye'],
                @endif
                ['Dokumen ISO', 'iso document dokumen', '{{ route('iso-documents.index') }}', 'bi-file-earmark-richtext'],
                @if(auth()->user() && !auth()->user()->isEmployee())
                ['Kelola Tinta', 'ink tinta stock stok', '{{ route('ink.index') }}', 'bi-droplet'],
                ['Kelola Sparepart', 'sparepart parts stok', '{{ route('spareparts.index') }}', 'bi-wrench-adjustable'],
                ['Kelola Lisensi', 'license lisensi seat', '{{ route('licenses.index') }}', 'bi-key'],
                ['Kelola CCTV', 'cctv kamera camera', '{{ route('cctv.index') }}', 'bi-camera-video'],
                ['Limbah IT', 'waste limbah', '{{ route('it-wastes.index') }}', 'bi-recycle'],
                ['Laporan', 'report laporan', '{{ route('reports.annual') }}', 'bi-bar-chart-line'],
                @if(auth()->user()->isMaster())
                ['Pengaturan User', 'user account akun', '{{ route('users.index') }}', 'bi-people'],
                @endif
                @endif
            ];
            const renderResults = () => {
                const keyword = search.value.trim().toLowerCase();
                if (!keyword) { results.hidden = true; results.innerHTML = ''; return; }
                const matches = pages.filter(page => (page[0] + ' ' + page[1]).toLowerCase().includes(keyword)).slice(0, 6);
                results.innerHTML = matches.length ? matches.map(page => '<a href="' + page[2] + '"><i class="bi ' + page[3] + '"></i><span>' + page[0] + '</span><small>Open page</small></a>').join('') : '<div class="global-search-empty">Tidak ada halaman yang cocok</div>';
                results.hidden = false;
            };
            search.addEventListener('input', renderResults);
            search.addEventListener('keydown', event => { if (event.key === 'Enter') { const first = results.querySelector('a'); if (first) window.location.href = first.href; } if (event.key === 'Escape') { search.value = ''; renderResults(); search.blur(); } });
            document.addEventListener('click', event => { if (!search.closest('.topbar-search').contains(event.target)) results.hidden = true; });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const translations = { 'Dashboard Utama':'Dashboard', 'Campaign':'Campaigns', 'To-do List':'Task List', 'Web Monitoring':'Web Monitoring', 'Peralatan IT / Asset':'IT Equipment / Assets', 'Mutasi Peralatan':'Equipment Transfers', 'Perbaikan IT / Ticketing':'IT Repair / Ticketing', 'Checklist':'Checklists', 'Jadwal':'Schedules', 'Inovasi IT':'IT Innovation', 'Pemantauan Sasaran':'Target Monitoring', 'Dokumen ISO':'ISO Documents', 'Pengaturan User':'User Settings', 'Profil Saya':'My Profile', 'Tanda Tangan Digital':'Digital Signature', 'Aktifkan notifikasi tiket':'Enable ticket notifications', 'Keluar':'Log out', 'Operasional':'Operations', 'Perencanaan':'Planning', 'General':'General', 'Pengaturan':'Settings' };
            const reverse = Object.fromEntries(Object.entries(translations).map(([id, en]) => [en, id]));
            const replaceText = language => {
                document.querySelectorAll('.sidebar-link,.sidebar-label,.sidebar-signature-link,.sidebar-alert-toggle,.topbar-logout').forEach(element => {
                    const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT);
                    const nodes = []; while (walker.nextNode()) nodes.push(walker.currentNode);
                    nodes.forEach(node => { const key = node.nodeValue.trim(); const value = language === 'en' ? translations[key] : reverse[key]; if (value) node.nodeValue = node.nodeValue.replace(key, value); });
                });
                const input = document.getElementById('globalPageSearch'); if (input) input.placeholder = language === 'en' ? 'Search pages...' : 'Cari halaman...';
            };
            replaceText(localStorage.getItem('it-monitoring-language') || 'id');
            document.getElementById('topbarPopover')?.addEventListener('click', event => { const choice = event.target.closest('[data-language]'); if (choice) setTimeout(() => replaceText(choice.dataset.language), 0); });
        });
    </script>
    <script>
        (() => {
            const toggle = document.getElementById('sidebarCollapseToggle');
            if (!toggle) return;
            const body = document.body;
            const savedState = localStorage.getItem('it-monitoring-sidebar-collapsed') === 'true';
            const setCollapsed = collapsed => {
                body.classList.toggle('sidebar-collapsed', collapsed);
                toggle.setAttribute('aria-expanded', String(!collapsed));
                toggle.setAttribute('aria-label', collapsed ? 'Tampilkan navigasi' : 'Sembunyikan navigasi');
                toggle.title = collapsed ? 'Tampilkan navigasi' : 'Sembunyikan navigasi';
                toggle.innerHTML = '<i class="bi ' + (collapsed ? 'bi-layout-sidebar' : 'bi-layout-sidebar-inset') + '"></i>';
            };
            setCollapsed(savedState);
            toggle.addEventListener('click', () => {
                const collapsed = !body.classList.contains('sidebar-collapsed');
                setCollapsed(collapsed);
                localStorage.setItem('it-monitoring-sidebar-collapsed', String(collapsed));
            });
        })();

        const ticketEndpoint = @json(route('it-repair-tickets.notifications'));
        const transferEndpoint = @json(route('equipment-transfers.notifications'));
        const companyLogoUrl = @json(asset('images/logo-mgm.svg'));
        const companyName = 'PT MULIA GRAND MANUFACTURE';
        const ticketBadge = document.getElementById('ticketNotificationBadge');
        const ticketProgressBadge = document.getElementById('ticketProgressBadge');
        const transferPendingApprovalBadge = document.getElementById('transferPendingApprovalBadge');
        const transferMyUnfinishedBadge = document.getElementById('transferMyUnfinishedBadge');
        const ticketToast = document.getElementById('ticketToast');
        const ticketToastMessage = document.getElementById('ticketToastMessage');
        const transferToast = document.getElementById('transferToast');
        const transferToastMessage = document.getElementById('transferToastMessage');
        const storedTicketIdKey = 'it-monitoring-last-ticket-id';
        const storedTransferIdKey = 'it-monitoring-last-transfer-id';
        let topbarTicketCount = 0;
        let topbarTransferCount = 0;
        function updateTopbarNotificationCount() {
            const badge = document.getElementById('topbarNotificationCount');
            if (!badge) return;
            const count = topbarTicketCount + topbarTransferCount;
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.dataset.zero = count === 0 ? 'true' : 'false';
        }

        function updateTicketNotifications() {
            fetch(ticketEndpoint, { headers: { Accept: 'application/json' } })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    const count = Number(data.openCount || 0);
                    topbarTicketCount = count;
                    updateTopbarNotificationCount();
                    ticketBadge.textContent = count;
                    ticketBadge.classList.toggle('d-none', count === 0);
                    const progressCount = Number(data.inProgressCount || 0);
                    ticketProgressBadge.textContent = progressCount;
                    ticketProgressBadge.classList.toggle('d-none', progressCount === 0);
                    if (!data.latest) return;
                    const previousId = sessionStorage.getItem(storedTicketIdKey);
                    if (previousId && Number(data.latest.id) > Number(previousId)) {
                        const message = data.latest.number + ' - ' + data.latest.equipment;
                        ticketToastMessage.textContent = message;
                        ticketToast.classList.add('show');
                        setTimeout(() => ticketToast.classList.remove('show'), 7000);
                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification('Tiket Perbaikan IT Baru', { body: message });
                        }
                    }
                    sessionStorage.setItem(storedTicketIdKey, data.latest.id);
                })
                .catch(() => {});
        }

        function updateTransferNotifications() {
            if (!transferPendingApprovalBadge || !transferMyUnfinishedBadge) return;
            fetch(transferEndpoint, { headers: { Accept: 'application/json' } })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    const pendingCount = Number(data.pendingApprovalCount || 0);
                    topbarTransferCount = pendingCount;
                    updateTopbarNotificationCount();
                    transferPendingApprovalBadge.textContent = pendingCount;
                    transferPendingApprovalBadge.classList.toggle('d-none', pendingCount === 0);

                    const myUnfinishedCount = Number(data.myUnfinishedCount || 0);
                    transferMyUnfinishedBadge.textContent = myUnfinishedCount;
                    transferMyUnfinishedBadge.classList.toggle('d-none', myUnfinishedCount === 0);

                    if (!data.latestPendingApproval) return;
                    const previousId = sessionStorage.getItem(storedTransferIdKey);
                    if (previousId && Number(data.latestPendingApproval.id) > Number(previousId)) {
                        const message = (data.latestPendingApproval.equipment || 'Peralatan') + ' - menunggu persetujuan';
                        transferToastMessage.textContent = message;
                        transferToast.classList.add('show');
                        setTimeout(() => transferToast.classList.remove('show'), 7000);
                        if ('Notification' in window && Notification.permission === 'granted') {
                            new Notification('Mutasi Peralatan Baru', { body: message });
                        }
                    }
                    sessionStorage.setItem(storedTransferIdKey, data.latestPendingApproval.id);
                })
                .catch(() => {});
        }

        document.getElementById('enableTicketAlerts').addEventListener('click', () => {
            if (!('Notification' in window)) return;
            Notification.requestPermission();
        });
        updateTicketNotifications();
        updateTransferNotifications();
        setInterval(updateTicketNotifications, 20000);
        setInterval(updateTransferNotifications, 20000);

        function tableSortValue(cell) {
            const primaryAssetName = cell.querySelector?.('.asset-cell strong')?.textContent;
            const value = (primaryAssetName || cell.textContent).trim().toLowerCase();
            if (/^-?\d+(?:[.,]\d+)?$/.test(value)) {
                return Number(value.replace(',', '.'));
            }
            return value;
        }

        function tableExportTitle() {            return (document.querySelector('.container h2, .container h3')?.textContent || document.title || 'data').trim();
        }

        function tableExportClone(table) {
            const clone = table.cloneNode(true);
            Array.from(clone.tBodies[0]?.rows || []).forEach(row => { if (row.style.display === 'none') row.remove(); });
            const headerRow = clone.tHead?.rows?.[0];
            if (headerRow) {
                const actionIndexes = Array.from(headerRow.cells).map((cell, index) => /aksi|action/i.test(cell.textContent) ? index : -1).filter(index => index >= 0).reverse();
                actionIndexes.forEach(index => {
                    Array.from(clone.rows).forEach(row => { if (row.cells[index]) row.deleteCell(index); });
                });
            }
            clone.querySelectorAll('img, form, button, .btn').forEach(element => element.remove());
            clone.querySelectorAll('th').forEach(cell => { cell.className = ''; cell.removeAttribute('role'); cell.removeAttribute('tabindex'); });
            clone.removeAttribute('class');
            clone.setAttribute('border', '1');
            clone.style.borderCollapse = 'collapse';
            return clone;
        }

        function tableExportFileName(extension) {
            return tableExportTitle().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') + '-' + new Date().toISOString().slice(0, 10) + '.' + extension;
        }

        function exportTableToExcel(table) {
            const html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body><h2>' + companyName + '</h2><h3>' + tableExportTitle() + '</h3><p>Dicetak: ' + new Date().toLocaleString('id-ID') + '</p>' + tableExportClone(table).outerHTML + '</body></html>';
            const link = document.createElement('a');
            link.href = URL.createObjectURL(new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel' }));
            link.download = tableExportFileName('xls');
            document.body.appendChild(link);
            link.click();
            setTimeout(() => { URL.revokeObjectURL(link.href); link.remove(); }, 1000);
        }

        function printTableDocument(table) {
            const frame = document.createElement('iframe');
            frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
            document.body.appendChild(frame);
            const doc = frame.contentWindow.document;
            doc.open();
            doc.write('<html><head><meta charset="utf-8"><title>' + tableExportTitle() + '</title><style>@page{size:landscape;margin:12mm}body{font-family:Arial,Helvetica,sans-serif;color:#17324d}.letterhead{display:flex;align-items:center;gap:12px;padding-bottom:8px;margin-bottom:10px;border-bottom:3px solid #0b5ea8}.letterhead img{width:46px;height:46px}.letterhead strong{display:block;font-size:15px;letter-spacing:.04em}.letterhead span{display:block;font-size:10px;color:#64748b}h3{margin:0 0 4px}p.meta{margin:0 0 12px;font-size:11px;color:#64748b}table{width:100%;border-collapse:collapse;font-size:11px}th,td{border:1px solid #94a3b8;padding:5px 7px;text-align:left;vertical-align:top}th{background:#e8f1fa}small{display:block;color:#64748b}</style></head><body><div class="letterhead"><img src="' + companyLogoUrl + '" alt="Logo"><div><strong>PT MULIA GRAND MANUFACTURE</strong><span>IT Monitoring &amp; Maintenance System</span></div></div><h3>' + tableExportTitle() + '</h3><p class="meta">Dicetak: ' + new Date().toLocaleString('id-ID') + '</p>' + tableExportClone(table).outerHTML + '</body></html>');
            doc.close();
            frame.contentWindow.focus();
            frame.contentWindow.print();
            setTimeout(() => frame.remove(), 1000);
        }

        function runTableExport(type, table) {
            if (type === 'excel') return exportTableToExcel(table);
            return printTableDocument(table);
        }
        window.ItTableExport = runTableExport;

        function buildTableToolbarButtons(table, fullDataUrl) {
            const group = document.createElement('div');
            group.className = 'data-table-export';
            [['excel', 'Excel', 'btn-outline-success'], ['pdf', 'PDF', 'btn-outline-danger'], ['print', 'Print', 'btn-outline-secondary']].forEach(([type, label, styleClass]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-sm ' + styleClass;
                button.textContent = label;
                button.addEventListener('click', () => {
                    if (fullDataUrl) {
                        const target = new URL(fullDataUrl, window.location.origin);
                        target.searchParams.set('export', type);
                        window.location.href = target.toString();
                        return;
                    }
                    runTableExport(type, table);
                });
                group.appendChild(button);
            });
            return group;
        }

        function enhanceDataTables() {
            document.querySelectorAll('table.table').forEach(table => {
                if (table.dataset.enhanced || table.classList.contains('no-table-tools') || table.closest('form') || table.querySelector('input[type="radio"], input[type="checkbox"], select, textarea') || table.querySelector('th[rowspan], th[colspan]')) return;
                const firstHeader = table.tHead?.rows?.[0]?.cells?.[0];
                if (firstHeader && /^(no\.|nomor|no)$/.test((firstHeader.textContent || '').trim().toLowerCase().replace(/\s+/g, ''))) {
                    table.dataset.enhanced = 'true';
                    return;
                }
                const body = table.tBodies[0];
                const headers = Array.from(table.tHead?.rows?.[0]?.cells || []);
                if (!body || !headers.length) return;
                table.dataset.enhanced = 'true';
                const page = Number(new URLSearchParams(window.location.search).get('page') || 1);
                const rowOffset = (Math.max(page, 1) - 1) * 50;
                const numberHeader = document.createElement('th');
                numberHeader.textContent = 'No.';
                numberHeader.className = 'table-row-number';
                table.tHead.rows[0].insertBefore(numberHeader, table.tHead.rows[0].firstChild);
                Array.from(body.rows).forEach((row, rowIndex) => {
                    const numberCell = document.createElement('td');
                    numberCell.className = 'table-row-number';
                    numberCell.textContent = rowOffset + rowIndex + 1;
                    row.insertBefore(numberCell, row.firstChild);
                });
                const wrapper = table.closest('.table-responsive');
                let scope = (wrapper || table).parentElement;
                let paginationEl = null;
                while (scope && !paginationEl) {
                    paginationEl = scope.querySelector('.table-pagination');
                    scope = scope.parentElement;
                }
                const serverPaginated = !!paginationEl;
                const hasMorePages = !!paginationEl?.querySelector('.pagination');
                const params = new URLSearchParams(window.location.search);
                const currentSearch = params.get('search') || '';
                const toolbar = document.createElement('div');
                toolbar.className = 'data-table-tools';
                let fullDataUrl = '';
                if (hasMorePages && params.get('per_page') !== 'all') {
                    const fullParams = new URLSearchParams(params);
                    fullParams.set('per_page', 'all');
                    fullParams.delete('page');
                    fullDataUrl = window.location.pathname + '?' + fullParams.toString();
                }
                toolbar.appendChild(buildTableToolbarButtons(table, fullDataUrl));
                if (serverPaginated) {
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = window.location.pathname;
                    form.className = 'data-table-search-form';
                    params.forEach((value, key) => {
                        if (key === 'page' || key === 'search' || key === 'export') return;
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = key;
                        hidden.value = value;
                        form.appendChild(hidden);
                    });
                    const input = document.createElement('input');
                    input.type = 'search';
                    input.name = 'search';
                    input.value = currentSearch;
                    input.autocomplete = 'off';
                    input.placeholder = 'Cari di semua data...';
                    input.setAttribute('aria-label', 'Cari di semua data');
                    form.appendChild(input);
                    let searchTimer;
                    input.addEventListener('input', () => {
                        clearTimeout(searchTimer);
                        searchTimer = setTimeout(() => form.submit(), 450);
                    });
                    form.addEventListener('submit', () => clearTimeout(searchTimer));
                    toolbar.appendChild(form);
                    (wrapper || table).parentElement.insertBefore(toolbar, wrapper || table);
                    if (currentSearch && !table.dataset.searchFocused) {
                        table.dataset.searchFocused = 'true';
                        input.focus();
                        try { input.setSelectionRange(currentSearch.length, currentSearch.length); } catch (error) { /* input type tanpa dukungan caret */ }
                    }
                } else {
                    const searchInput = document.createElement('input');
                    searchInput.type = 'search';
                    searchInput.placeholder = 'Cari di tabel...';
                    searchInput.setAttribute('aria-label', 'Cari di tabel');
                    toolbar.appendChild(searchInput);
                    (wrapper || table).parentElement.insertBefore(toolbar, wrapper || table);
                    searchInput.addEventListener('input', () => {
                        const keyword = searchInput.value.trim().toLowerCase();
                        Array.from(body.rows).forEach(row => { row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none'; });
                    });
                }
                headers.forEach((header, index) => {
                    if (index === headers.length - 1 && /aksi|action/i.test(header.textContent)) return;
                    header.classList.add('table-sortable');
                    header.setAttribute('role', 'button');
                    header.setAttribute('tabindex', '0');
                    let direction = 1;
                    const sort = () => {
                        const rows = Array.from(body.rows).filter(row => row.style.display !== 'none');
                        rows.sort((first, second) => {
                            const firstValue = tableSortValue(first.cells[index + 1] || first.cells[0]);
                            const secondValue = tableSortValue(second.cells[index + 1] || second.cells[0]);
                            return typeof firstValue === 'number' && typeof secondValue === 'number' ? (firstValue - secondValue) * direction : String(firstValue).localeCompare(String(secondValue), 'id', { numeric: true, sensitivity: 'base' }) * direction;
                        });
                        rows.forEach(row => body.appendChild(row));
                        headers.forEach(item => item.classList.remove('sort-asc', 'sort-desc'));
                        header.classList.add(direction === 1 ? 'sort-asc' : 'sort-desc');
                        direction *= -1;
                    };
                    header.addEventListener('click', sort);
                    header.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); sort(); } });
                });
            });
        }
        enhanceDataTables();

        (function runPendingExport() {
            const params = new URLSearchParams(window.location.search);
            const type = params.get('export');
            if (!type) return;
            const table = document.querySelector('table[data-enhanced="true"]');
            if (!table) return;
            params.delete('export');
            const query = params.toString();
            window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : ''));
            setTimeout(() => runTableExport(type, table), 300);
        })();
</script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const page = document.querySelector('.ink-page, .sparepart-page, .license-page, .cctv-page, .waste-page');
            if (!page) return;
            const formRow = page.querySelector('.row.g-4');
            const statsRow = page.querySelector('.row.g-3.mb-4');
            if (!formRow || !statsRow) return;
            const modal = document.createElement('div');
            modal.className = 'crud-modal';
            modal.innerHTML = '<div class="crud-modal-backdrop"></div><div class="crud-modal-dialog" role="dialog" aria-modal="true" aria-label="Form Kelola"><div class="crud-modal-head"><div><span class="crud-modal-kicker">WORKSPACE FORM</span><h2 id="crudModalTitle">Kelola data</h2></div><button type="button" class="crud-modal-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button></div><div class="crud-modal-body"></div></div>';
            document.body.appendChild(modal);
            const body = modal.querySelector('.crud-modal-body');
            const title = modal.querySelector('#crudModalTitle');
            const sections = Array.from(formRow.querySelectorAll(':scope > [class*="col-"] > .card, :scope > [class*="col-"] > section'));
            formRow.classList.add('crud-modal-source');
            const actionBar = document.createElement('div');
            actionBar.className = 'crud-action-bar';
            actionBar.innerHTML = '<div><span class="crud-action-kicker">QUICK ACTIONS</span><strong>Kelola data operasional</strong><small>Pilih workspace untuk menambah master data atau mencatat pergerakan.</small></div>';
            sections.forEach((section, index) => {
                const header = section.querySelector('.card-header');
                const heading = header?.querySelector('h2')?.textContent.trim() || (index === 0 ? 'Form Master Data' : 'Form Transaksi');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'crud-action-button';
                button.innerHTML = '<i class="bi ' + (index === 0 ? 'bi-plus-circle' : 'bi-arrow-left-right') + '"></i><span><strong>' + heading + '</strong><small>Buka form</small></span><i class="bi bi-arrow-up-right"></i>';
                button.addEventListener('click', () => { title.textContent = heading; body.innerHTML = ''; body.appendChild(section); modal.classList.add('is-open'); document.body.classList.add('modal-open'); });
                actionBar.appendChild(button);
            });
            statsRow.after(actionBar);
            modal.querySelectorAll('.crud-modal-close, .crud-modal-backdrop').forEach(element => element.addEventListener('click', () => { modal.classList.remove('is-open'); document.body.classList.remove('modal-open'); }));
            page.querySelectorAll('table tbody tr').forEach(row => {
                const actionCell = row.lastElementChild;
                if (!actionCell || row.querySelector('.crud-detail-button') || row.querySelector('.empty-state')) return;
                const detailButton = document.createElement('button');
                detailButton.type = 'button';
                detailButton.className = 'btn btn-sm btn-outline-secondary crud-detail-button';
                detailButton.innerHTML = '<i class="bi bi-eye"></i> Detail';
                detailButton.addEventListener('click', () => {
                    title.textContent = 'Detail data'; body.innerHTML = '<div class="crud-detail-grid">' + Array.from(row.cells).slice(0, -1).map((cell, cellIndex) => '<div><span>Informasi ' + (cellIndex + 1) + '</span><strong>' + cell.textContent.trim() + '</strong></div>').join('') + '</div>'; modal.classList.add('is-open'); document.body.classList.add('modal-open');
                });
                actionCell.appendChild(detailButton);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const routePatterns = ['/equipments/', '/equipment-transfers/', '/it-repair-tickets/', '/maintenance-checklists/', '/web-monitoring-checklists/', '/maintenances/', '/monthly-schedules/', '/innovations/', '/users/', '/masters/', '/iso-documents/', '/campaigns/', '/todo-list/'];
            const formEndings = ['/create', '/edit', '/repair'];
            const links = Array.from(document.querySelectorAll('a[href]')).filter(link => { const pathname = new URL(link.href, window.location.origin).pathname; return routePatterns.some(pattern => pathname.includes(pattern)) && formEndings.some(ending => pathname.endsWith(ending)); });
            if (!links.length) return;
            const modal = document.createElement('div');
            modal.className = 'route-form-modal';
            modal.innerHTML = '<div class="route-form-backdrop"></div><div class="route-form-dialog" role="dialog" aria-modal="true"><div class="route-form-head"><div><span>QUICK CREATE WORKSPACE</span><h2>Form input</h2></div><button type="button" class="route-form-close" aria-label="Tutup"><i class="bi bi-x-lg"></i></button></div><div class="route-form-loading"><i class="bi bi-arrow-repeat"></i><span>Menyiapkan form...</span></div><iframe title="Form input" class="route-form-frame"></iframe></div>';
            document.body.appendChild(modal);
            const frame = modal.querySelector('.route-form-frame');
            const loading = modal.querySelector('.route-form-loading');
            const heading = modal.querySelector('.route-form-head h2');
            const close = () => { modal.classList.remove('is-open'); document.body.classList.remove('modal-open'); frame.src = 'about:blank'; };
            modal.querySelector('.route-form-close').addEventListener('click', close);
            modal.querySelector('.route-form-backdrop').addEventListener('click', close);
            frame.addEventListener('load', () => {
                try {
                    const frameDocument = frame.contentDocument;
                    const shellStyle = frameDocument.createElement('style');
                    shellStyle.textContent = '.app-sidebar,.app-topbar,.app-footer,.sidebar-toggle,.print-letterhead{display:none!important}.app-main{margin-left:0!important;padding:24px!important}.app-main>.container,.app-main>.container-fluid{max-width:none!important;width:100%!important}.route-form-page,.repair-form-page,.transfer-page,.maintenance-check-page,.web-check-page,.schedule-page,.monthly-schedule-page,.innovation-page{margin-top:0!important}';
                    frameDocument.head.appendChild(shellStyle);
                    frameDocument.body.classList.remove('modal-open');
                    frameDocument.querySelectorAll('form').forEach(form => { form.target = '_top'; });
                } catch (error) {
                    console.warn('Form modal shell cleanup failed', error);
                }
                loading.style.display = 'none';
                frame.style.display = 'block';
            });
            links.forEach(link => link.addEventListener('click', event => {
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === '_blank') return;
                event.preventDefault();
                const url = new URL(link.href, window.location.origin);
                heading.textContent = link.textContent.trim() || 'Form input';
                loading.style.display = 'flex'; frame.style.display = 'none'; frame.src = url.href;
                modal.classList.add('is-open'); document.body.classList.add('modal-open');
            }));
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const popover = document.getElementById('topbarPopover');
            const buttons = {
                language: document.getElementById('topbarLanguageButton'),
                apps: document.getElementById('topbarAppsButton'),
                notifications: document.getElementById('topbarAlertButton'),
                settings: document.getElementById('topbarSettingsButton'),
            };
            if (!popover) return;
            const closePopover = () => { popover.hidden = true; popover.innerHTML = ''; };
            const openPopover = (html) => { popover.innerHTML = html; popover.hidden = false; };
            const openLanguage = () => openPopover('<div class="topbar-popover-heading">Pilih Bahasa</div><button class="popover-option" data-language="id"><i class="bi bi-check-circle"></i><span>Bahasa Indonesia</span><small>ID</small></button><button class="popover-option" data-language="en"><i class="bi bi-circle"></i><span>English</span><small>EN</small></button><div class="popover-note">Pilihan bahasa disimpan di browser ini.</div>');
            const openApps = () => openPopover('<div class="topbar-popover-heading">Akses Cepat</div>@if(auth()->user() && !auth()->user()->isEmployee())<a class="popover-link" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard Utama</a>@if(auth()->user()->isMaster())<a class="popover-link" href="{{ route('campaigns.index') }}"><i class="bi bi-megaphone"></i>Campaign</a><a class="popover-link" href="{{ route('todo-list.index') }}"><i class="bi bi-check2-square"></i>To-do List</a>@endif<a class="popover-link" href="{{ route('web-monitoring.index') }}"><i class="bi bi-globe2"></i>Web Monitoring</a><a class="popover-link" href="{{ route('equipments.index') }}"><i class="bi bi-laptop"></i>Peralatan IT</a>@endif<a class="popover-link" href="{{ route('it-repair-tickets.index') }}"><i class="bi bi-tools"></i>Ticketing</a><a class="popover-link" href="{{ route('iso-documents.index') }}"><i class="bi bi-file-earmark-richtext"></i>Dokumen ISO</a>');
            const openNotifications = () => {
                const ticketCount = document.getElementById('ticketNotificationBadge')?.textContent || '0';
                const transferCount = document.getElementById('transferPendingApprovalBadge')?.textContent || '0';
                openPopover('<div class="topbar-popover-heading">Notifikasi</div><a class="popover-link" href="{{ route('it-repair-tickets.index') }}"><i class="bi bi-tools"></i><span>Tiket terbuka</span><strong>' + ticketCount + '</strong></a>@if(auth()->user() && !auth()->user()->isEmployee())<a class="popover-link" href="{{ route('equipment-transfers.index') }}"><i class="bi bi-arrow-left-right"></i><span>Mutasi menunggu approval</span><strong>' + transferCount + '</strong></a>@endif<button id="popoverNotificationPermission" class="popover-action"><i class="bi bi-bell"></i>Aktifkan notifikasi browser</button>');
                document.getElementById('popoverNotificationPermission')?.addEventListener('click', () => document.getElementById('enableTicketAlerts')?.click());
            };
            const openSettings = () => openPopover('<div class="topbar-popover-heading">Pengaturan Cepat</div><button id="popoverSidebarToggle" class="popover-link"><i class="bi bi-layout-sidebar-inset"></i><span>Hide / show navbar</span><strong>' + (document.body.classList.contains('sidebar-collapsed') ? 'Show' : 'Hide') + '</strong></button><a class="popover-link" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i><span>Profil Saya</span><strong><i class="bi bi-arrow-up-right"></i></strong></a><div class="popover-note">Pengaturan lanjutan tersedia di profil akun.</div>');
            buttons.language?.addEventListener('click', event => { event.stopPropagation(); popover.hidden ? openLanguage() : closePopover(); });
            buttons.apps?.addEventListener('click', event => { event.stopPropagation(); popover.hidden ? openApps() : closePopover(); });
            buttons.notifications?.addEventListener('click', event => { event.stopPropagation(); popover.hidden ? openNotifications() : closePopover(); });
            buttons.settings?.addEventListener('click', event => { event.stopPropagation(); popover.hidden ? openSettings() : closePopover(); });
            popover.addEventListener('click', event => {
                const languageButton = event.target.closest('[data-language]');
                if (languageButton) { localStorage.setItem('it-monitoring-language', languageButton.dataset.language); document.documentElement.lang = languageButton.dataset.language; showToast(languageButton.dataset.language === 'en' ? 'Language set to English' : 'Bahasa diatur ke Indonesia'); closePopover(); }
                const sidebarButton = event.target.closest('#popoverSidebarToggle');
                if (sidebarButton) { document.getElementById('sidebarCollapseToggle')?.click(); closePopover(); }
            });
            document.addEventListener('click', event => { if (!popover.hidden && !popover.contains(event.target) && !Object.values(buttons).some(button => button?.contains(event.target))) closePopover(); });
            document.documentElement.lang = localStorage.getItem('it-monitoring-language') || 'id';
        });
    </script>
</body>
</html>
