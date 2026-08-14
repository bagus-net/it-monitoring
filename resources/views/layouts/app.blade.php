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
                <a class="sidebar-link" href="{{ route('dashboard') }}">Dashboard Utama</a>
                <a class="sidebar-link" href="{{ route('web-monitoring.index') }}">Web Monitoring</a>
                <a class="sidebar-link" href="{{ route('equipments.index') }}">Peralatan IT</a>
                <a class="sidebar-link sidebar-ticket-link" href="{{ route('it-repair-tickets.index') }}"><span>Perbaikan IT</span><span id="ticketNotificationBadge" class="ticket-notification-badge d-none">0</span></a>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Checklist</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('web-monitoring-checklists.index') }}">Checklist Web Monitoring</a></li><li><a class="dropdown-item" href="{{ route('maintenance-checklists.index') }}">Pelaksanaan Checklist IT</a></li></ul></div>
                <a class="sidebar-link" href="{{ route('maintenances.grid') }}">Grid Perawatan</a>
                <a class="sidebar-link" href="{{ route('activity-logs.index') }}">Log Aktivitas User</a>
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

        function tableSortValue(cell) {
            const primaryAssetName = cell.querySelector?.('.asset-cell strong')?.textContent;
            const value = (primaryAssetName || cell.textContent).trim().toLowerCase();
            if (/^-?\d+(?:[.,]\d+)?$/.test(value)) {
                return Number(value.replace(',', '.'));
            }
            return value;
        }

        function enhanceDataTables() {
            document.querySelectorAll('table.table').forEach(table => {
                if (table.dataset.enhanced || table.classList.contains('no-table-tools') || table.closest('form') || table.querySelector('input[type="radio"], input[type="checkbox"], select, textarea') || table.querySelector('th[rowspan], th[colspan]')) return;
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
                const toolbar = document.createElement('div');
                toolbar.className = 'data-table-tools';
                toolbar.innerHTML = '<input type="search" placeholder="Cari di tabel..." aria-label="Cari di tabel">';
                const wrapper = table.closest('.table-responsive');
                (wrapper || table).parentElement.insertBefore(toolbar, wrapper || table);
                const searchInput = toolbar.querySelector('input');
                searchInput.addEventListener('input', () => {
                    const keyword = searchInput.value.trim().toLowerCase();
                    Array.from(body.rows).forEach(row => { row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none'; });
                });
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
    </script>
</body>
</html>
