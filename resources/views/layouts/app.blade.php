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
    <button class="sidebar-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Buka navigasi">Menu</button>
    <aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="appSidebar">
        <div class="offcanvas-header sidebar-mobile-header">
            <strong>IT Monitoring</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-block">
            <a class="sidebar-brand" href="{{ route('dashboard') }}"><span class="sidebar-brand-mark">IT</span><span>IT Monitoring<small>Asset & Maintenance</small></span></a>
            <nav class="sidebar-nav">
                <span class="sidebar-label">Operasional</span>
                <a class="sidebar-link" href="{{ route('dashboard') }}">Web Monitoring</a>
                <a class="sidebar-link" href="{{ route('web-monitoring-checklists.index') }}">Checklist Web</a>
                <a class="sidebar-link" href="{{ route('equipments.index') }}">Peralatan IT</a>
                <a class="sidebar-link sidebar-ticket-link" href="{{ route('it-repair-tickets.index') }}"><span>Perbaikan IT</span><span id="ticketNotificationBadge" class="ticket-notification-badge d-none">0</span></a>
                <a class="sidebar-link" href="{{ route('maintenance-checklists.index') }}">Pelaksanaan Checklist</a>
                <a class="sidebar-link" href="{{ route('maintenances.grid') }}">Grid Perawatan</a>
                <span class="sidebar-label">Perencanaan</span>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Jadwal</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('maintenances.schedules') }}">Jadwal Tahunan</a></li><li><a class="dropdown-item" href="{{ route('monthly_schedules.index') }}">Jadwal Bulanan</a></li></ul></div>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Laporan</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('reports.annual') }}">Laporan Tahunan</a></li><li><a class="dropdown-item" href="{{ route('reports.monthly') }}">Laporan Bulanan</a></li></ul></div>
                <span class="sidebar-label">Master Data</span>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Pengaturan Master</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('masters.manufacturers.index') }}">Manufacturers</a></li><li><a class="dropdown-item" href="{{ route('masters.locations.index') }}">Lokasi</a></li><li><a class="dropdown-item" href="{{ route('masters.equipment-types.index') }}">Tipe Peralatan</a></li><li><a class="dropdown-item" href="{{ route('masters.checklist-items.index') }}">Program Perawatan</a></li></ul></div>
            </nav>
            <div class="sidebar-footer"><button id="enableTicketAlerts" type="button" class="sidebar-alert-toggle">Aktifkan notifikasi tiket</button><span>IT Maintenance System</span></div>
        </div>
    </aside>
    <main class="app-main py-4">
        @if(session('success'))
            <div class="container-fluid app-content"><div class="alert alert-success">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="container-fluid app-content"><div class="alert alert-danger">{{ session('error') }}</div></div>
        @endif
        @yield('content')
    </main>
    <div id="ticketToast" class="ticket-toast" role="status"><strong>Tiket baru masuk</strong><span id="ticketToastMessage"></span><a href="{{ route('it-repair-tickets.index') }}">Buka tiket</a></div>
    <script>
        const ticketEndpoint = @json(route('it-repair-tickets.notifications'));
        const ticketBadge = document.getElementById('ticketNotificationBadge');
        const ticketToast = document.getElementById('ticketToast');
        const ticketToastMessage = document.getElementById('ticketToastMessage');
        const storedTicketIdKey = 'it-monitoring-last-ticket-id';

        function updateTicketNotifications() {
            fetch(ticketEndpoint, { headers: { Accept: 'application/json' } })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    const count = Number(data.openCount || 0);
                    ticketBadge.textContent = count;
                    ticketBadge.classList.toggle('d-none', count === 0);
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

        document.getElementById('enableTicketAlerts').addEventListener('click', () => {
            if (!('Notification' in window)) return;
            Notification.requestPermission();
        });
        updateTicketNotifications();
        setInterval(updateTicketNotifications, 20000);
    </script>
</body>
</html>
