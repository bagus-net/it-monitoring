@extends('layouts.app')

@section('content')
<style>
  /* Minimal dashboard-specific styles kept here to match existing theme */
  .wrap{max-width:1180px;margin:0 auto;padding:24px;}
  .summary-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;}
  .stat-card{background:#121821;border:1px solid #232D3A;border-radius:12px;padding:16px 18px;color:#E7EDF5}
  .stat-card .label{font-size:11px;color:#8493A6;text-transform:uppercase}
  .stat-card .value{font-family:IBM Plex Mono,monospace;font-size:28px;margin-top:6px}
  .stat-card.up .value{color:#35D48A}
  .stat-card.down .value{color:#FF5D5D}
  .panel{background:#121821;border:1px solid #232D3A;border-radius:12px;padding:18px 20px;margin-bottom:22px;color:#E7EDF5}
  .panel-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
  .panel-head h2{font-size:13px;color:#8493A6;margin:0;text-transform:uppercase}
  .site-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
  .site-card{background:#171F2A;border:1px solid #232D3A;border-radius:10px;padding:14px 16px}
  .site-name{font-weight:600;font-size:14px}
  .site-url{color:#8493A6;font-size:11px;font-family:IBM Plex Mono,monospace;margin-top:2px}
  .ping-strip{display:flex;gap:2px;margin-top:12px;align-items:flex-end;height:22px}
  .ping-tick{flex:1;min-width:3px;border-radius:1px;background:#232D3A;height:100%}
  .ping-tick.UP{background:#35D48A}
  .ping-tick.DOWN{background:#FF5D5D}
  .ping-tick.ERROR{background:#F5A623}
  .toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(20px);background:#171F2A;border:1px solid #232D3A;color:#E7EDF5;padding:10px 18px;border-radius:8px;font-size:13px;opacity:0;pointer-events:none;transition:all .25s ease;z-index:50}
  .toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
</style>

<div class="wrap">

  <div class="summary-grid">
    <div class="stat-card"><div class="label">Total Situs</div><div class="value" id="statTotal">0</div></div>
    <div class="stat-card up"><div class="label">Up</div><div class="value" id="statUp">0</div></div>
    <div class="stat-card down"><div class="label">Down</div><div class="value" id="statDown">0</div></div>
    <div class="stat-card"><div class="label">Rata-rata Response</div><div class="value" id="statAvg">-</div></div>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>Tambah Situs Dipantau</h2></div>
    <form class="add-form" onsubmit="handleAddSite(event)">
      <input type="text" id="siteName" placeholder="Nama situs (mis. Website Utama)">
      <input type="text" id="siteUrl" placeholder="https://contoh.com" required>
      <button type="submit" class="primary">+ Tambah</button>
    </form>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Status Situs</h2>
      <span class="mono" style="color:#8493A6;font-size:11px;" id="siteCountLabel"></span>
    </div>
    <div id="siteGrid" class="site-grid"></div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Tren Response Time</h2>
      <span class="mono" style="color:#8493A6;font-size:11px;" id="chartSiteLabel">pilih situs di atas</span>
    </div>
    <div class="chart-wrap" style="height:260px;position:relative;"><canvas id="rtChart"></canvas></div>
  </div>

  <div class="panel">
    <div class="panel-head"><h2>Log Terbaru</h2></div>
    <div class="table-scroll">
      <table>
        <thead><tr><th>Waktu</th><th>Situs</th><th>Kode</th><th>Response</th><th>Status</th></tr></thead>
        <tbody id="logsBody"></tbody>
      </table>
    </div>
  </div>

</div>

<div class="toast" id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
  let currentData = null;
  let selectedSiteId = null;
  let chart = null;

  function fmtTime(ts){ return new Date(ts).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'}); }
  function fmtDateTime(ts){ return new Date(ts).toLocaleString('id-ID',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}); }
  function showToast(msg){
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 2400);
  }

  async function jsonFetch(url, options = {}){
    const res = await fetch(url, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
      },
      ...options,
    });
    if(!res.ok){
      const body = await res.json().catch(()=>({}));
      throw new Error(body.message || ('HTTP ' + res.status));
    }
    return res.json();
  }

  function loadDashboard(){
    jsonFetch('{{ route('dashboard.data') }}').then(renderAll).catch(onError);
  }

  function refreshNow(){
    const btn = document.querySelector('.primary[onclick="refreshNow()"]') || document.getElementById('refreshBtn');
    if(btn) { btn.disabled = true; btn.textContent = 'Mengecek...'; }
    jsonFetch('{{ route('dashboard.check-now') }}', { method: 'POST' })
      .then(data => { renderAll(data); showToast('Pengecekan selesai'); })
      .catch(onError)
      .finally(() => { if(btn){ btn.disabled = false; btn.textContent = 'Cek Sekarang'; } });
  }

  function onError(err){ showToast('Error: ' + err.message); console.error(err); }

  function handleAddSite(e){
    e.preventDefault();
    const name = document.getElementById('siteName').value.trim();
    const url = document.getElementById('siteUrl').value.trim();
    if(!url) return;
    jsonFetch('{{ route('sites.store') }}', { method: 'POST', body: JSON.stringify({ name, url }) })
      .then(() => {
        document.getElementById('siteName').value = '';
        document.getElementById('siteUrl').value = '';
        showToast('Situs ditambahkan'); loadDashboard();
      })
      .catch(onError);
  }

  function deleteSite(id, evt){ evt.stopPropagation(); if(!confirm('Hapus situs ini dari pemantauan?')) return; jsonFetch('/sites/' + id, { method: 'DELETE' }).then(()=>{ showToast('Situs dihapus'); loadDashboard(); }).catch(onError); }

  function renderAll(data){
    currentData = data;
    const brandSpan = document.querySelector('.brand span');
    if(brandSpan) brandSpan.textContent = 'diperbarui ' + fmtTime(data.generatedAt);
    document.getElementById('statTotal').textContent = data.summary.totalSites;
    document.getElementById('statUp').textContent = data.summary.upSites;
    document.getElementById('statDown').textContent = data.summary.downSites;
    document.getElementById('statAvg').textContent = data.summary.avgResponse ? data.summary.avgResponse + ' ms' : '-';
    document.getElementById('siteCountLabel').textContent = data.sites.length + ' situs';
    renderSiteGrid(data.sites);
    renderLogs(data.recentLogs);
    if(!selectedSiteId && data.sites.length) selectedSiteId = data.sites[0].id;
    renderChart();
  }

  function renderSiteGrid(sites){
    const grid = document.getElementById('siteGrid');
    if(!sites.length){ grid.innerHTML = '<div class="empty-state">Belum ada situs. Tambahkan URL di atas untuk mulai memantau.</div>'; return; }
    grid.innerHTML = sites.map(site => {
      const status = site.lastStatus || 'PENDING';
      const ticks = site.history.length ? site.history.map(h => '<div class="ping-tick ' + h.status + '"></div>').join('') : '<div style="font-size:10px;color:#8493A6;">belum ada riwayat</div>';
      const rt = site.lastResponseMs ? site.lastResponseMs + ' ms' : '-';
      const up = site.uptimePct != null ? site.uptimePct + '%' : '-';
      return '<div class="site-card ' + (site.id === selectedSiteId ? 'selected' : '') + '" onclick="selectSite(' + site.id + ')">' +
        '<div class="site-card-top"><div><div class="site-name">' + escapeHtml(site.name) + '</div><div class="site-url">' + escapeHtml(site.url) + '</div></div>' +
        '<span class="status-badge ' + status + '">' + status + '</span></div>' +
        '<div class="site-metrics"><div><div class="m-label">Response</div><div class="m-value">' + rt + '</div></div><div><div class="m-label">Uptime</div><div class="m-value">' + up + '</div></div></div>' +
        '<div class="ping-strip">' + ticks + '</div>' +
        '<div class="card-actions"><span style="font-size:10px;color:#8493A6;font-family:\'IBM Plex Mono\',monospace;">' + (site.active ? 'aktif' : 'nonaktif') + '</span><button class="ghost" onclick="deleteSite(' + site.id + ', event)">Hapus</button></div></div>';
    }).join('');
  }

  function selectSite(id){ selectedSiteId = id; renderSiteGrid(currentData.sites); renderChart(); }

  function renderChart(){
    const site = (currentData.sites || []).find(s => s.id === selectedSiteId);
    const ctxEl = document.getElementById('rtChart');
    if(!ctxEl) return;
    const ctx = ctxEl.getContext('2d');
    const label = document.getElementById('chartSiteLabel');
    if(!site || !site.history.length){ label.textContent = site ? site.name + ' - belum ada data' : 'pilih situs di atas'; if(chart) chart.destroy(); chart = null; return; }
    label.textContent = site.name;
    const labels = site.history.map(h => fmtTime(h.t));
    const values = site.history.map(h => h.ms);
    if(chart) chart.destroy();
    chart = new Chart(ctx, { type: 'line', data: { labels, datasets: [{ label: 'Response time (ms)', data: values, borderColor: '#4C8DFF', backgroundColor: 'rgba(76,141,255,0.08)', pointRadius: 3, tension: 0.3, fill: true }]}, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: '#8493A6', maxTicksLimit: 8 }, grid: { color: '#232D3A' } }, y: { ticks: { color: '#8493A6' }, grid: { color: '#232D3A' }, beginAtZero: true } } } });
  }

  function renderLogs(logs){ const body = document.getElementById('logsBody'); if(!logs.length){ body.innerHTML = '<tr><td colspan="5" style="color:#8493A6">Belum ada log.</td></tr>'; return; } body.innerHTML = logs.map(l => '<tr><td>' + fmtDateTime(l.t) + '</td><td>' + escapeHtml(l.name) + '</td><td>' + (l.code || '-') + '</td><td>' + l.ms + ' ms</td><td class="status-' + l.status + '">' + l.status + '</td></tr>').join(''); }

  function escapeHtml(str){ const div = document.createElement('div'); div.textContent = str || ''; return div.innerHTML; }

  loadDashboard();
  setInterval(loadDashboard, 60000);
</script>

@endsection
