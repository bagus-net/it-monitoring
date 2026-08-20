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
            <strong>PT Mulia Grand Manufacture</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-block">
            @php($currentUser = auth()->user())
            <a class="sidebar-brand" href="{{ $currentUser && $currentUser->isEmployee() ? route('it-repair-tickets.index') : route('dashboard') }}"><span class="sidebar-brand-mark"><img src="{{ asset('images/logo-mgm.svg') }}" alt="Logo PT Mulia Grand Manufacture"></span><span>PT Mulia Grand Manufacture<small>IT Monitoring &amp; Maintenance</small></span></a>
            <nav class="sidebar-nav">
                @if($currentUser && !$currentUser->isEmployee())
                <a class="sidebar-link" href="{{ route('dashboard') }}">Dashboard Utama</a>
                @endif
                <span class="sidebar-label">Operasional</span>

                @if($currentUser && !$currentUser->isEmployee())
                <a class="sidebar-link" href="{{ route('web-monitoring.index') }}">Web Monitoring</a>
                <a class="sidebar-link" href="{{ route('equipments.index') }}">Peralatan IT / Asset</a>
                <a class="sidebar-link" href="{{ route('equipment-transfers.index') }}">Mutasi Peralatan</a>
                @endif
                <a class="sidebar-link sidebar-ticket-link" href="{{ route('it-repair-tickets.index') }}"><span>Perbaikan IT / Ticketing</span><span class="ticket-badge-group"><span id="ticketNotificationBadge" class="ticket-notification-badge d-none" title="Tiket open">0</span><span id="ticketProgressBadge" class="ticket-notification-badge badge-progress d-none" title="Tiket sedang dikerjakan">0</span></span></a>
                @if($currentUser && !$currentUser->isEmployee())
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Checklist</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('web-monitoring-checklists.index') }}">Web Monitoring</a></li><li><a class="dropdown-item" href="{{ route('maintenance-checklists.index') }}">Peralatan IT</a></li></ul></div>
                {{-- <a class="sidebar-link" href="{{ route('maintenances.grid') }}">Grid Perawatan</a> --}}

                <span class="sidebar-label">Perencanaan</span>
                @if($currentUser->isMaster())
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Jadwal</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('maintenances.schedules') }}">Jadwal Tahunan</a></li><li><a class="dropdown-item" href="{{ route('monthly_schedules.index') }}">Jadwal Bulanan</a></li></ul></div>
                @endif
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Laporan</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('reports.annual') }}">Laporan Tahunan</a></li><li><a class="dropdown-item" href="{{ route('reports.monthly') }}">Laporan Bulanan</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="{{ route('reports.equipments') }}">Laporan Peralatan IT</a></li><li><a class="dropdown-item" href="{{ route('reports.repairs') }}">Laporan Perbaikan IT</a></li><li><a class="dropdown-item" href="{{ route('reports.checklists') }}">Laporan Checklist Web &amp; Peralatan IT</a></li>@if($currentUser->isMaster())<li><a class="dropdown-item" href="{{ route('reports.activities') }}">Laporan Log Aktivitas</a></li>@endif</ul></div>
                <span class="sidebar-label">Master Data</span>
                <div class="sidebar-dropdown dropdown"><button class="sidebar-link sidebar-link-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown">Pengaturan Master</button><ul class="dropdown-menu sidebar-menu"><li><a class="dropdown-item" href="{{ route('masters.manufacturers.index') }}">Manufacturers</a></li><li><a class="dropdown-item" href="{{ route('masters.locations.index') }}">Lokasi</a></li><li><a class="dropdown-item" href="{{ route('masters.equipment-types.index') }}">Tipe Peralatan</a></li><li><a class="dropdown-item" href="{{ route('masters.checklist-items.index') }}">Program Perawatan</a></li></ul></div>
                @endif
                @if($currentUser && $currentUser->isMaster())
                <span class="sidebar-label">Pengaturan</span>
                <a class="sidebar-link" href="{{ route('users.index') }}">Pengaturan User</a>
                <a class="sidebar-link" href="{{ route('activity-logs.index') }}">Log Aktivitas User</a>
                @endif
            </nav>
            <div class="sidebar-footer">
                @auth
                <div class="sidebar-user"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->roleLabel() }}</span></div>
                <a class="sidebar-signature-link" href="{{ route('signature.edit') }}">Tanda Tangan Digital</a>
                @endauth
                <button id="enableTicketAlerts" type="button" class="sidebar-alert-toggle">Aktifkan notifikasi tiket</button>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="sidebar-logout">Keluar</button></form>
                <span>IT Maintenance System</span>
            </div>
        </div>
    </aside>
    <main class="app-main py-4">
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
    </main>
    <div id="ticketToast" class="ticket-toast" role="status"><strong>Tiket baru masuk</strong><span id="ticketToastMessage"></span><a href="{{ route('it-repair-tickets.index') }}">Buka tiket</a></div>
    <script>
        const ticketEndpoint = @json(route('it-repair-tickets.notifications'));
        const companyLogoUrl = @json(asset('images/logo-mgm.svg'));
        const companyName = 'PT MULIA GRAND MANUFACTURE';
        const ticketBadge = document.getElementById('ticketNotificationBadge');
        const ticketProgressBadge = document.getElementById('ticketProgressBadge');
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
</body>
</html>
