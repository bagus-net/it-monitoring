@extends('layouts.app')

@section('content')
<style>
  .monitoring-dashboard{max-width:1440px;margin:0 auto;padding:0 4px}.operations-overview{margin-bottom:22px}.operations-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;margin-bottom:14px}.operations-heading h1{margin:4px 0;font-size:1.7rem;color:#17324d}.operations-heading p{margin:0;color:#64748b}.operations-date{color:#64748b;font-size:.78rem}.operations-grid{display:grid;grid-template-columns:repeat(5,minmax(150px,1fr));gap:12px}.operation-card{display:block;padding:15px;background:#fff;border:1px solid #dbe5ef;border-top:4px solid #0b5ea8;color:#17324d;text-decoration:none;transition:transform .15s,box-shadow .15s}.operation-card:hover{color:#17324d;transform:translateY(-2px);box-shadow:0 8px 16px rgba(15,23,42,.1)}.operation-card span,.operation-card small{display:block;color:#64748b;font-size:.74rem}.operation-card strong{display:block;font-size:1.45rem;margin:4px 0}.operation-card.web{border-top-color:#0b5ea8}.operation-card.webcheck{border-top-color:#7c3aed}.operation-card.asset{border-top-color:#f59e0b}.operation-card.ticket{border-top-color:#dc2626}.operation-card.maintenance{border-top-color:#159957}.operations-feed{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px}.operations-panel{margin-bottom:0}.panel-link{color:#0b5ea8;font-size:.78rem;font-weight:700;text-decoration:none}.operation-list{display:grid;gap:0}.operation-row{display:flex;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid #edf2f7;color:#17324d;text-decoration:none}.operation-row:last-child{border-bottom:0}.operation-row strong,.operation-row span,.operation-row small{display:block}.operation-row strong{font-size:.82rem}.operation-row span,.operation-row small{color:#64748b;font-size:.74rem}.operation-row span{max-width:360px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.operation-row div:last-child{text-align:right;flex-shrink:0}.op-status{display:inline-block;padding:3px 6px;border-radius:3px;font-size:.7rem;font-style:normal;font-weight:700}.op-status-open{background:#fef3c7;color:#92400e}.op-status-in_progress{background:#dbeafe;color:#1d4ed8}.op-status-resolved{background:#dcfce7;color:#166534}.operation-empty{padding:18px;color:#64748b;text-align:center}.monitoring-head{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:22px}.monitoring-eyebrow{color:#0b5ea8;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.monitoring-head h1{margin:4px 0;font-size:1.7rem;color:#17324d}.monitoring-head h2{margin:4px 0;font-size:1.25rem;color:#17324d}.monitoring-head p{margin:0;color:#64748b}.monitoring-actions{display:flex;gap:8px}.monitoring-actions .btn{white-space:nowrap}.summary-grid{display:grid;grid-template-columns:repeat(4,minmax(150px,1fr));gap:14px;margin-bottom:18px}.stat-card{position:relative;overflow:hidden;background:#fff;border:1px solid #dbe5ef;border-radius:7px;padding:17px 18px;color:#17324d}.stat-card:before{content:'';position:absolute;top:0;left:0;width:100%;height:4px;background:#0b5ea8}.stat-card.up:before{background:#159957}.stat-card.down:before{background:#dc2626}.stat-card.response:before{background:#f6b322}.stat-card .label{font-size:.72rem;color:#64748b;text-transform:uppercase;letter-spacing:.05em;font-weight:700}.stat-card .value{font-size:1.7rem;font-weight:700;margin-top:5px}.stat-card .hint{margin-top:2px;font-size:.75rem;color:#94a3b8}.panel{background:#fff;border:1px solid #dbe5ef;border-radius:7px;padding:18px 20px;margin-bottom:18px;color:#17324d}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}.panel-head h2{font-size:.95rem;color:#17324d;margin:0}.panel-subtitle{font-size:.78rem;color:#64748b}.live-indicator{color:#15803d;font-size:.72rem;font-weight:700}.live-indicator:before{content:'';display:inline-block;width:7px;height:7px;margin-right:5px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 3px #dcfce7}.add-form{display:grid;grid-template-columns:minmax(180px,.7fr) minmax(260px,1.4fr) auto;gap:10px}.add-form input{min-width:0;border:1px solid #cbd5e1;border-radius:5px;padding:9px 11px;font-size:.9rem}.primary{border:0;border-radius:5px;background:#0b5ea8;color:#fff;padding:9px 13px;font-weight:700}.primary:hover{background:#084e8d}.site-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:13px}.site-card{cursor:pointer;background:#fbfdff;border:1px solid #dbe5ef;border-radius:7px;padding:15px;transition:border .18s,box-shadow .18s,transform .18s}.site-card:hover,.site-card.selected{border-color:#0b5ea8;box-shadow:0 8px 18px rgba(11,94,168,.12);transform:translateY(-1px)}.site-card-top{display:flex;justify-content:space-between;gap:10px}.site-name{font-weight:700;font-size:.95rem;color:#17324d}.site-url{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:190px;color:#64748b;font-size:.73rem;margin-top:3px}.status-badge{display:inline-block;padding:4px 7px;border-radius:3px;font-size:.7rem;font-weight:700}.status-badge.UP{background:#dcfce7;color:#166534}.status-badge.DOWN{background:#fee2e2;color:#991b1b}.status-badge.ERROR{background:#fef3c7;color:#92400e}.status-badge.PENDING{background:#e2e8f0;color:#475569}.site-metrics{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:13px}.m-label{font-size:.7rem;color:#64748b}.m-value{font-size:.92rem;font-weight:700;color:#17324d}.ping-strip{display:flex;gap:2px;margin-top:13px;height:18px}.ping-tick{flex:1;min-width:3px;border-radius:2px;background:#e2e8f0}.ping-tick.UP{background:#22c55e}.ping-tick.DOWN{background:#ef4444}.ping-tick.ERROR{background:#f6b322}.card-actions{display:flex;align-items:center;justify-content:space-between;margin-top:11px}.ghost{border:0;background:none;color:#dc2626;font-size:.76rem;padding:0}.dashboard-split{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(340px,.75fr);gap:18px}.chart-wrap{height:270px;position:relative}.table-scroll{overflow:auto}.table-scroll table{width:100%;border-collapse:collapse;font-size:.82rem}.table-scroll th{text-align:left;padding:8px;border-bottom:1px solid #dbe5ef;color:#64748b;font-size:.72rem;text-transform:uppercase}.table-scroll td{padding:9px 8px;border-bottom:1px solid #edf2f7}.status-UP{color:#15803d;font-weight:700}.status-DOWN,.status-ERROR{color:#dc2626;font-weight:700}.empty-state{padding:26px;color:#64748b;border:1px dashed #cbd5e1;border-radius:6px;text-align:center}.toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(20px);background:#17324d;color:#fff;padding:10px 18px;border-radius:6px;font-size:13px;opacity:0;pointer-events:none;transition:all .25s ease;z-index:1100}.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}@media(max-width:1100px){.operations-grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:900px){.summary-grid{grid-template-columns:repeat(2,1fr)}.dashboard-split,.operations-feed{grid-template-columns:1fr}}@media(max-width:600px){.operations-heading,.monitoring-head{flex-direction:column;align-items:flex-start}.operations-grid{grid-template-columns:1fr 1fr}.monitoring-actions{width:100%}.monitoring-actions .btn{flex:1}.add-form{grid-template-columns:1fr}.summary-grid{gap:9px}.stat-card{padding:13px}.stat-card .value{font-size:1.35rem}.panel{padding:14px}.site-grid{grid-template-columns:1fr}.chart-wrap{height:220px}.operation-row span{max-width:180px}}
</style>

<style>
  .operations-grid{grid-template-columns:repeat(5,minmax(150px,1fr))}.operation-card.inventory{border-top-color:#0891b2}.operation-card.transfer{border-top-color:#f97316}.operation-card.license{border-top-color:#6d28d9}
  .trend-chart{height:215px;min-width:0;overflow-x:auto;overflow-y:hidden;padding:18px 8px 0;grid-template-columns:repeat({{ $dashboardTrend->count() }},minmax(48px,1fr));grid-auto-flow:column}.trend-chart>div{min-width:48px}.trend-column{height:178px;align-items:flex-end}.trend-bar{position:relative;display:flex;align-items:flex-start;justify-content:center;min-height:4px;box-shadow:0 4px 8px rgba(15,23,42,.12)}.trend-bar b{position:absolute;top:-18px;color:#475569;font-size:.62rem;font-weight:800}.trend-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:14px}.trend-summary-item{padding:9px 10px;border-radius:5px;background:#f8fafc}.trend-summary-item span,.trend-summary-item strong{display:block}.trend-summary-item span{color:#64748b;font-size:.68rem}.trend-summary-item strong{margin-top:2px;color:#17324d;font-size:1rem}.trend-summary-item.ticket{border-left:3px solid #dc2626}.trend-summary-item.checklist{border-left:3px solid #7c3aed}.trend-summary-item.stock{border-left:3px solid #0891b2}.trend-summary-item.license{border-left:3px solid #f59e0b}
  .trend-filter{display:flex;align-items:center;gap:5px;flex-wrap:wrap;justify-content:flex-end}.trend-filter label{color:#64748b;font-size:.72rem}.trend-filter select,.trend-filter button,.download-chart{height:30px;padding:3px 7px;border:1px solid #cbd5e1;border-radius:4px;background:#fff;color:#475569;font-size:.72rem}.trend-filter button{border-color:#0b5ea8;background:#0b5ea8;color:#fff;font-weight:700}.trend-filter button:hover{background:#084e8d}.download-chart{border-color:#0b5ea8;color:#0b5ea8;font-weight:700;white-space:nowrap}.download-chart:hover{background:#edf5fc}@media(max-width:700px){.trend-filter{width:100%;justify-content:flex-start;margin-top:10px}.trend-filter select{flex:1;min-width:0}}
  .dashboard-analytics{display:grid;grid-template-columns:minmax(0,1.7fr) minmax(280px,1fr);gap:18px;margin-bottom:22px}.analytics-panel{padding:18px;background:#fff;border:1px solid #dbe5ef}.analytics-head{display:flex;justify-content:space-between;gap:10px;align-items:flex-start;margin-bottom:16px}.analytics-head h2{margin:0;color:#17324d;font-size:1rem}.analytics-head p{margin:4px 0 0;color:#64748b;font-size:.78rem}.trend-chart{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;height:190px;padding:12px 8px 0;border-bottom:1px solid #cbd5e1;background:repeating-linear-gradient(to bottom,transparent 0,transparent 46px,#edf2f7 47px)}.trend-column{display:flex;align-items:flex-end;justify-content:center;gap:4px;height:160px}.trend-bar{width:11px;min-height:3px;border-radius:3px 3px 0 0}.trend-bar.ticket{background:#dc2626}.trend-bar.checklist{background:#7c3aed}.trend-bar.stock{background:#0891b2}.trend-bar.license{background:#f59e0b}.trend-label{text-align:center;color:#64748b;font-size:.72rem;margin-top:7px}.chart-legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:12px;color:#64748b;font-size:.72rem}.chart-legend i{display:inline-block;width:9px;height:9px;margin-right:4px;border-radius:2px}.health-layout{display:flex;align-items:center;gap:22px}.health-donut{width:132px;height:132px;flex:0 0 132px;border-radius:50%;background:conic-gradient(#16a34a {{ $overview['assets'] ? round(($assetStatus['Normal'] / max(1, $overview['assets'])) * 100) : 0 }}%,#f59e0b 0);position:relative}.health-donut::after{content:'';position:absolute;inset:25px;border-radius:50%;background:#fff}.health-list{display:grid;gap:10px;width:100%}.health-item{display:flex;justify-content:space-between;gap:10px;color:#475569;font-size:.78rem}.health-item b{color:#17324d}.risk-list{display:grid;gap:8px;margin-top:12px}.risk-item{display:flex;justify-content:space-between;gap:10px;padding:9px 10px;border-left:3px solid #f59e0b;background:#fffbeb;color:#475569;font-size:.78rem}.risk-item strong{color:#92400e}.quick-links{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}.quick-links a{padding:7px 10px;border:1px solid #cbd5e1;border-radius:4px;color:#0b5ea8;font-size:.74rem;font-weight:700;text-decoration:none}.quick-links a:hover{background:#edf5fc}@media(max-width:1100px){.operations-grid{grid-template-columns:repeat(3,minmax(150px,1fr))}.dashboard-analytics{grid-template-columns:1fr}}@media(max-width:600px){.operations-grid{grid-template-columns:repeat(2,minmax(130px,1fr))}.dashboard-analytics{display:block}.analytics-panel{margin-bottom:14px}.trend-chart{gap:4px}.trend-bar{width:7px}.health-layout{gap:12px}.health-donut{width:105px;height:105px;flex-basis:105px}}
</style>

<style>
  /* Web Monitoring now lives on its dedicated /web-monitoring page. */
  .monitoring-dashboard > .monitoring-head,
  .monitoring-dashboard > .summary-grid,
  .monitoring-dashboard > .monitoring-head ~ .panel,
  .monitoring-dashboard > .monitoring-head ~ .dashboard-split { display: none; }
</style>

<style>
  .monitoring-dashboard{max-width:1480px;padding:0 8px;color:#18243d}
  .monitoring-dashboard:before{content:'';display:block;position:fixed;inset:0;z-index:-1;background:#f7f9fc}
  .operations-overview{margin-bottom:24px}
  .operations-heading{align-items:center;margin:4px 0 22px;padding:0 4px}
  .monitoring-eyebrow{color:#2161f5;font-size:.68rem;font-weight:800;letter-spacing:.13em;text-transform:uppercase}
  .operations-heading h1{margin:5px 0 7px;color:#172039;font-size:clamp(1.55rem,2.5vw,2.15rem);font-weight:800;letter-spacing:-.02em}
  .operations-heading p{color:#8490a7;font-size:.86rem}
  .operations-date{padding:9px 13px;border:1px solid #e7ebf2;border-radius:10px;background:#fff;color:#7c879c;font-size:.72rem;box-shadow:0 3px 12px rgba(35,52,85,.04)}
  .operations-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
  .operation-card{position:relative;min-height:118px;padding:18px;border:1px solid #e9edf4;border-top:0;border-radius:14px;background:#fff;color:#18243d;box-shadow:0 5px 18px rgba(35,52,85,.05);overflow:hidden}
  .operation-card:after{content:'';position:absolute;right:-22px;bottom:-32px;width:88px;height:88px;border-radius:50%;background:currentColor;opacity:.055}
  .operation-card:hover{color:#18243d;transform:translateY(-3px);box-shadow:0 12px 25px rgba(35,52,85,.11)}
  .operation-card span{color:#78849a;font-size:.74rem;font-weight:700}
  .operation-card strong{margin:8px 0 3px;color:#172039;font-size:1.72rem;line-height:1;font-weight:800}
  .operation-card small{color:#8792a7;font-size:.68rem}
  .operation-card.web{color:#2161f5;border-top:0}.operation-card.webcheck{color:#8957e8;border-top:0}.operation-card.asset{color:#f0a429;border-top:0}.operation-card.ticket{color:#ea5b67;border-top:0}.operation-card.maintenance{color:#25a873;border-top:0}.operation-card.inventory{color:#159db2;border-top:0}.operation-card.transfer{color:#e97d31;border-top:0}.operation-card.license{color:#755be4;border-top:0}
  .operation-card{padding-right:66px}.operation-icon{position:absolute;top:16px;right:16px;width:40px;height:40px;flex:0 0 40px;display:flex;align-items:center;justify-content:center;border-radius:11px;background:color-mix(in srgb,currentColor 14%,#fff);font-size:1.05rem}
  @media(max-width:700px){.operation-card{padding-right:58px}.operation-icon{width:34px;height:34px;top:14px;right:14px;font-size:.92rem}}
  .dashboard-analytics{grid-template-columns:minmax(0,1.55fr) minmax(320px,.85fr);gap:16px;margin-bottom:16px}
  .analytics-panel,.panel{border:1px solid #e9edf4;border-radius:14px;background:#fff;box-shadow:0 5px 18px rgba(35,52,85,.045)}
  .analytics-panel{padding:20px}
  .analytics-head{margin-bottom:18px}.analytics-head h2,.panel-head h2{color:#18243d;font-size:.98rem;font-weight:800}.analytics-head p,.panel-subtitle{color:#8a95a8;font-size:.72rem}
  .trend-chart{border-bottom-color:#e9edf4;background:repeating-linear-gradient(to bottom,transparent 0,transparent 46px,#f0f3f7 47px)}
  .trend-bar{border-radius:5px 5px 0 0;box-shadow:none}.trend-bar.ticket{background:#ef6671}.trend-bar.checklist{background:#8b65e8}.trend-bar.stock{background:#36b6c1}.trend-bar.license{background:#f2b34c}
  .trend-filter select{border:1px solid #e7ebf2;border-radius:10px;background:#f7f9fc;color:#3f4a63;font-weight:600}
  .trend-filter button{border-radius:10px;font-weight:700}.download-chart{border-radius:10px}
  .chart-legend{gap:8px}.chart-legend span{display:inline-flex;align-items:center;padding:5px 11px;border-radius:999px;background:#f7f9fc;font-weight:600;color:#57627a}.chart-legend i{width:7px;height:7px;border-radius:50%}
  .trend-summary-item{position:relative;border-radius:12px;padding:11px 14px 11px 24px;border-left:0}.trend-summary-item:before{content:'';position:absolute;left:13px;top:15px;width:7px;height:7px;border-radius:50%}.trend-summary-item strong{color:#18243d}
  .trend-summary-item.ticket{background:color-mix(in srgb,#ef6671 9%,#f7f9fc);border-left:0}.trend-summary-item.ticket:before{background:#ef6671}
  .trend-summary-item.checklist{background:color-mix(in srgb,#8b65e8 9%,#f7f9fc);border-left:0}.trend-summary-item.checklist:before{background:#8b65e8}
  .trend-summary-item.stock{background:color-mix(in srgb,#36b6c1 9%,#f7f9fc);border-left:0}.trend-summary-item.stock:before{background:#36b6c1}
  .trend-summary-item.license{background:color-mix(in srgb,#f2b34c 9%,#f7f9fc);border-left:0}.trend-summary-item.license:before{background:#f2b34c}
  .health-donut{background:conic-gradient(#27b47a {{ $overview['assets'] ? round(($assetStatus['Normal'] / max(1, $overview['assets'])) * 100) : 0 }}%,#f3b54b 0);box-shadow:0 0 0 10px #f8fafc}
  .health-list{gap:4px}.health-item{padding:8px 6px;border-radius:9px;transition:background .15s}.health-item:hover{background:#f7f9fc}.health-item span{display:flex;align-items:center;gap:8px;color:#657188;font-weight:600}.health-dot{width:8px;height:8px;flex:0 0 8px;border-radius:50%;background:#94a3b8}.health-item.normal .health-dot{background:#27b47a}.health-item.warning .health-dot{background:#f3b54b}.health-item.danger .health-dot{background:#ea5b67}
  .risk-list{gap:6px}.risk-item{align-items:center;border-radius:12px;border-left:0;padding:9px 12px;background:transparent;transition:background .15s}.risk-item:hover{background:#f7f9fc}.risk-item strong{color:#18243d;font-weight:800}.risk-icon{display:flex;align-items:center;justify-content:center;width:30px;height:30px;flex:0 0 30px;border-radius:9px;background:color-mix(in srgb,#f3b54b 16%,#fff);color:#c9821a;font-size:.86rem}.risk-label{flex:1 1 auto;color:#475569;font-weight:600}
  .quick-links{gap:8px}.quick-links a{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border:0;border-radius:999px;font-weight:700;transition:transform .15s,box-shadow .15s}.quick-links a:hover{transform:translateY(-1px)}.quick-links a.web{background:color-mix(in srgb,#2161f5 12%,#fff);color:#2161f5}.quick-links a.inventory{background:color-mix(in srgb,#159db2 12%,#fff);color:#159db2}.quick-links a.license{background:color-mix(in srgb,#755be4 12%,#fff);color:#755be4}
  .operations-feed{gap:16px;margin-bottom:22px}.operations-panel{padding:20px}.panel-head{margin-bottom:12px}.panel-link{color:#2161f5}.operation-row{padding:13px 0;border-bottom-color:#edf0f5}.operation-row strong{color:#26324b;font-size:.78rem}.operation-row span,.operation-row small{color:#8792a7}
  @media(max-width:1050px){.operations-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.dashboard-analytics{grid-template-columns:1fr}}
  @media(max-width:700px){.monitoring-dashboard{padding:0}.operations-heading{align-items:flex-start;flex-direction:column}.operations-date{align-self:stretch}.operations-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.operation-card{min-height:108px;padding:14px}.operation-card strong{font-size:1.42rem}.operations-feed{grid-template-columns:1fr}.analytics-panel,.operations-panel{padding:15px}.trend-summary{grid-template-columns:repeat(2,1fr)}}
  @media(max-width:420px){.operations-grid{grid-template-columns:1fr}.operation-card{min-height:96px}}
  .operations-heading{position:relative}.operations-heading:after{content:'IT OPERATIONS COMMAND CENTER';position:absolute;right:4px;top:-16px;color:#b7c0d1;font-size:.58rem;font-weight:800;letter-spacing:.14em}.operation-card{min-height:142px;padding:19px 18px;border:1px solid #e7ebf2;border-top:3px solid currentColor;border-radius:16px;background:linear-gradient(145deg,#fff 0%,#fbfcff 100%);box-shadow:0 8px 22px rgba(35,52,85,.06);transition:transform .18s,box-shadow .18s,border-color .18s}.operation-card:hover{border-color:currentColor;box-shadow:0 14px 28px rgba(35,52,85,.12)}.operation-card .operation-icon{top:17px;right:17px;width:38px;height:38px;border-radius:12px;background:color-mix(in srgb,currentColor 12%,#fff);font-size:.95rem}.operation-card span:not(.operation-icon):not(.operation-card-footer){color:#6f7c92;font-size:.72rem;font-weight:800}.operation-card strong{margin:10px 0 4px;font-size:1.75rem;letter-spacing:-.03em}.operation-card small{font-size:.66rem}.operation-card-footer{display:flex;align-items:center;gap:5px;margin-top:12px;padding-top:10px;border-top:1px solid #eef1f5;color:#8792a7!important;font-size:.62rem!important;font-weight:700!important}.operation-card-footer i{color:currentColor;font-size:.7rem}.operation-card.webcheck .operation-card-footer{color:#8957e8!important}.operation-card.asset .operation-card-footer{color:#f0a429!important}.operation-card.ticket .operation-card-footer{color:#ea5b67!important}.operation-card.maintenance .operation-card-footer{color:#25a873!important}.operation-card.inventory .operation-card-footer{color:#159db2!important}.operation-card.license .operation-card-footer{color:#755be4!important}.operation-card.transfer .operation-card-footer{color:#e97d31!important}
  @media(max-width:700px){.operations-heading:after{position:static;display:block;margin-top:10px}.operation-card{min-height:132px;padding:16px}.operation-card .operation-icon{top:14px;right:14px}}
</style>

<div class="monitoring-dashboard">

  <section class="operations-overview">
    <div class="operations-heading"><div><div class="monitoring-eyebrow">Dashboard Operasional</div><h1>Ringkasan IT Operations</h1><p>Ikhtisar Web Monitoring, checklist, aset, dan perbaikan IT dalam satu halaman.</p></div><div class="operations-date">{{ now()->format('d M Y H:i') }} WIB</div></div>
    <div class="operations-grid">
        <a class="operation-card web" href="{{ route('web-monitoring.index') }}"><span class="operation-icon"><i class="bi bi-globe2"></i></span><span>Web Monitoring</span><strong id="operationsWebStatus">Memuat...</strong><small>status situs dipantau</small><span class="operation-card-footer"><i class="bi bi-broadcast"></i> Live health check</span></a>
      <a class="operation-card webcheck" href="{{ route('web-monitoring-checklists.index') }}"><span class="operation-icon"><i class="bi bi-clipboard2-check"></i></span><span>Checklist Web</span><strong>{{ $overview['webChecklistMonth'] }}</strong><small>dokumen bulan ini</small><span class="operation-card-footer"><i class="bi bi-calendar-check"></i> Periode berjalan</span></a>
      <a class="operation-card asset" href="{{ route('equipments.index') }}"><span class="operation-icon"><i class="bi bi-laptop"></i></span><span>Aset IT</span><strong>{{ $overview['assets'] }}</strong><small>{{ $overview['assetAttention'] }} perlu perhatian</small><span class="operation-card-footer"><i class="bi bi-shield-check"></i> {{ $overview['assets'] ? round(($overview['assets'] - $overview['assetAttention']) / $overview['assets'] * 100) : 0 }}% sehat</span></a>
      <a class="operation-card ticket" href="{{ route('it-repair-tickets.index') }}"><span class="operation-icon"><i class="bi bi-tools"></i></span><span>Tiket Perbaikan</span><strong>{{ $overview['ticketsOpen'] }}</strong><small>{{ $overview['ticketsUrgent'] }} prioritas tinggi/mendesak</small><span class="operation-card-footer"><i class="bi bi-lightning-charge"></i> Triage operasional</span></a>
      <a class="operation-card maintenance" href="{{ route('maintenance-checklists.index') }}"><span class="operation-icon"><i class="bi bi-clipboard-check"></i></span><span>Pelaksanaan Checklist</span><strong>{{ $overview['maintenanceChecklistMonth'] }}</strong><small>dokumen bulan ini</small><span class="operation-card-footer"><i class="bi bi-check2-all"></i> Preventive maintenance</span></a>
      <a class="operation-card inventory" href="{{ route('ink.index') }}"><span class="operation-icon"><i class="bi bi-droplet"></i></span><span>Stok Tinta</span><strong>{{ $overview['inkLowStock'] }}</strong><small>jenis menipis</small><span class="operation-card-footer"><i class="bi bi-box-seam"></i> Inventory alert</span></a>
      <a class="operation-card inventory" href="{{ route('spareparts.index') }}"><span class="operation-icon"><i class="bi bi-wrench-adjustable"></i></span><span>Stok Sparepart</span><strong>{{ $overview['sparepartLowStock'] }}</strong><small>jenis menipis</small><span class="operation-card-footer"><i class="bi bi-box-seam"></i> Inventory alert</span></a>
      <a class="operation-card license" href="{{ route('licenses.index') }}"><span class="operation-icon"><i class="bi bi-key"></i></span><span>Seat Lisensi</span><strong>{{ $overview['licenseAvailableSeats'] }}</strong><small>seat tersedia</small><span class="operation-card-footer"><i class="bi bi-shield-lock"></i> License governance</span></a>
      <a class="operation-card transfer" href="{{ route('equipment-transfers.index') }}"><span class="operation-icon"><i class="bi bi-arrow-left-right"></i></span><span>Mutasi Peralatan</span><strong>{{ $overview['transfersPending'] }}</strong><small>menunggu proses</small><span class="operation-card-footer"><i class="bi bi-arrow-repeat"></i> Workflow approval</span></a>
    </div>
  </section>

  <section class="dashboard-analytics">
    <div class="analytics-panel">
      <div class="analytics-head"><div><h2>Tren Aktivitas Sistem</h2><p>Jumlah aktivitas berdasarkan periode yang dipilih.</p></div><div class="trend-controls"><form method="GET" action="{{ route('dashboard') }}" class="trend-filter"><label>Tampilkan</label><select name="period" aria-label="Panjang periode">@foreach($periodOptions as $value => $label)<option value="{{ $value }}" @selected($selectedPeriod === $value)>{{ $label }}</option>@endforeach</select><select name="month" aria-label="Bulan akhir">@foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $monthNumber => $monthName)<option value="{{ $monthNumber + 1 }}" @selected($selectedMonth === $monthNumber + 1)>{{ $monthName }}</option>@endforeach</select><select name="year" aria-label="Tahun akhir">@foreach($yearOptions as $year)<option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>@endforeach</select><button type="submit">Terapkan</button></form><button type="button" class="download-chart" onclick="downloadTrendChart()">Unduh Grafik</button></div></div>
      <div class="trend-chart">
        @php($trendMax = max(1, $dashboardTrend->flatMap(fn ($item) => [$item['tickets'], $item['checklists'], $item['stock'], $item['licenses']])->max()))
        @foreach($dashboardTrend as $trend)
          <div><div class="trend-column"><span class="trend-bar ticket" style="height:{{ round(($trend['tickets'] / $trendMax) * 150) }}px" title="Tiket: {{ $trend['tickets'] }}"><b>{{ $trend['tickets'] }}</b></span><span class="trend-bar checklist" style="height:{{ round(($trend['checklists'] / $trendMax) * 150) }}px" title="Checklist: {{ $trend['checklists'] }}"><b>{{ $trend['checklists'] }}</b></span><span class="trend-bar stock" style="height:{{ round(($trend['stock'] / $trendMax) * 150) }}px" title="Stok: {{ $trend['stock'] }}"><b>{{ $trend['stock'] }}</b></span><span class="trend-bar license" style="height:{{ round(($trend['licenses'] / $trendMax) * 150) }}px" title="Lisensi: {{ $trend['licenses'] }}"><b>{{ $trend['licenses'] }}</b></span></div><div class="trend-label">{{ $trend['label'] }}</div></div>
        @endforeach
      </div>
      <div class="chart-legend"><span><i style="background:#dc2626"></i>Tiket</span><span><i style="background:#7c3aed"></i>Checklist</span><span><i style="background:#0891b2"></i>Stok</span><span><i style="background:#f59e0b"></i>Lisensi</span></div>
      <div class="trend-summary"><div class="trend-summary-item ticket"><span>Total Tiket</span><strong>{{ $dashboardTrend->sum('tickets') }}</strong></div><div class="trend-summary-item checklist"><span>Total Checklist</span><strong>{{ $dashboardTrend->sum('checklists') }}</strong></div><div class="trend-summary-item stock"><span>Total Stok</span><strong>{{ $dashboardTrend->sum('stock') }}</strong></div><div class="trend-summary-item license"><span>Total Lisensi</span><strong>{{ $dashboardTrend->sum('licenses') }}</strong></div></div>
    </div>
    <div class="analytics-panel">
      <div class="analytics-head"><div><h2>Kesehatan Operasional</h2><p>Aset dan risiko yang perlu dipantau.</p></div></div>
      <div class="health-layout"><div class="health-donut"></div><div class="health-list"><div class="health-item normal"><span><i class="health-dot"></i>Aset normal</span><b>{{ $assetStatus['Normal'] }}</b></div><div class="health-item warning"><span><i class="health-dot"></i>Perlu perhatian</span><b>{{ $assetStatus['Perlu Perhatian'] }}</b></div><div class="health-item danger"><span><i class="health-dot"></i>Tiket aktif</span><b>{{ $assetStatus['Tiket Aktif'] }}</b></div></div></div>
      <div class="risk-list"><div class="risk-item"><span class="risk-icon"><i class="bi bi-exclamation-triangle"></i></span><span class="risk-label">Situs bermasalah</span><strong>{{ $overview['sitesDown'] }}</strong></div><div class="risk-item"><span class="risk-icon"><i class="bi bi-award"></i></span><span class="risk-label">Lisensi segera berakhir</span><strong>{{ $overview['licenseExpiring'] }}</strong></div><div class="risk-item"><span class="risk-icon"><i class="bi bi-arrow-left-right"></i></span><span class="risk-label">Mutasi belum selesai</span><strong>{{ $overview['transfersPending'] }}</strong></div></div>
      <div class="quick-links"><a class="web" href="{{ route('web-monitoring.index') }}"><i class="bi bi-globe2"></i>Pantau situs</a><a class="inventory" href="{{ route('ink.index') }}"><i class="bi bi-droplet"></i>Cek tinta</a><a class="inventory" href="{{ route('spareparts.index') }}"><i class="bi bi-wrench-adjustable"></i>Cek sparepart</a><a class="license" href="{{ route('licenses.index') }}"><i class="bi bi-key"></i>Cek lisensi</a></div>
    </div>
  </section>

  <section class="operations-feed">
    <div class="panel operations-panel"><div class="panel-head"><div><h2>Tiket Perbaikan Terbaru</h2><div class="panel-subtitle">Gangguan yang perlu ditindaklanjuti</div></div><a href="{{ route('it-repair-tickets.index') }}" class="panel-link">Semua tiket</a></div><div class="operation-list">@forelse($recentTickets as $ticket)<a href="{{ route('it-repair-tickets.show', $ticket) }}" class="operation-row"><div><strong>{{ $ticket->ticket_number }}</strong><span>{{ $ticket->equipment->name ?? '-' }} - {{ $ticket->problem_description }}</span></div><div><em class="op-status op-status-{{ $ticket->status }}">{{ ['open'=>'Open','in_progress'=>'Proses','resolved'=>'Selesai'][$ticket->status] }}</em><small>{{ $ticket->reported_at?->format('d M H:i') }}</small></div></a>@empty<div class="operation-empty">Belum ada tiket perbaikan.</div>@endforelse</div></div>
    <div class="panel operations-panel"><div class="panel-head"><div><h2>Checklist Terbaru</h2><div class="panel-subtitle">Web dan pelaksanaan perawatan</div></div></div><div class="operation-list">@if($recentWebChecklists->isEmpty() && $recentMaintenanceChecklists->isEmpty())<div class="operation-empty">Belum ada dokumen checklist.</div>@else @foreach($recentWebChecklists as $checklist)<a href="{{ route('web-monitoring-checklists.show', $checklist) }}" class="operation-row"><div><strong>Web: {{ $checklist->site->name }}</strong><span>{{ $checklist->checklist_type === 'security' ? 'Keamanan Web' : 'Fungsional Web' }}</span></div><div><small>{{ $checklist->checked_at?->format('d M H:i') }}</small></div></a>@endforeach @foreach($recentMaintenanceChecklists as $checklist)<a href="{{ route('maintenance-checklists.show', $checklist) }}" class="operation-row"><div><strong>IT: {{ $checklist->checklistItem->title }}</strong><span>Pelaksanaan Checklist Perawatan</span></div><div><small>{{ $checklist->checked_at?->format('d M Y') }}</small></div></a>@endforeach @endif</div></div>
  </section>

  <div class="monitoring-head">
    <div><div class="monitoring-eyebrow">Web Monitoring</div><h2>Ringkasan Kesehatan Layanan</h2><p>Status ketersediaan dan performa endpoint yang dipantau.</p></div>
    <div class="monitoring-actions"><a href="{{ route('web-monitoring-checklists.index') }}" class="btn btn-outline-primary">Checklist Web</a><button id="refreshBtn" class="btn btn-brand" type="button" onclick="refreshNow()">Cek Sekarang</button></div>
  </div>

  <div class="summary-grid">
    <div class="stat-card"><div class="label">Total Situs</div><div class="value" id="statTotal">0</div><div class="hint">Endpoint terdaftar</div></div>
    <div class="stat-card up"><div class="label">Layanan Normal</div><div class="value" id="statUp">0</div><div class="hint">Status terakhir UP</div></div>
    <div class="stat-card down"><div class="label">Perlu Perhatian</div><div class="value" id="statDown">0</div><div class="hint">DOWN atau ERROR</div></div>
    <div class="stat-card response"><div class="label">Rata-rata Respons</div><div class="value" id="statAvg">-</div><div class="hint">Pemeriksaan terakhir</div></div>
  </div>

  <div class="panel">
    <div class="panel-head"><div><h2>Tambah Situs Dipantau</h2><div class="panel-subtitle">Masukkan endpoint HTTP atau HTTPS untuk dipantau.</div></div></div>
    <form class="add-form" onsubmit="handleAddSite(event)">
      <input type="text" id="siteName" placeholder="Nama situs (mis. Website Utama)">
      <input type="text" id="siteUrl" placeholder="https://contoh.com" required>
      <button type="submit" class="primary">+ Tambah</button>
    </form>
  </div>

  <div class="panel">
    <div class="panel-head">
      <div><h2>Status Situs</h2><div class="panel-subtitle">Pilih kartu situs untuk melihat tren respons.</div></div>
      <span class="mono" style="color:#8493A6;font-size:11px;" id="siteCountLabel"></span>
    </div>
    <div id="siteGrid" class="site-grid"></div>
  </div>

  <div class="dashboard-split">
    <div class="panel">
      <div class="panel-head"><div><h2>Tren Response Time</h2><div class="panel-subtitle" id="chartSiteLabel">Pilih situs di atas</div></div><span class="live-indicator">Live</span></div>
      <div class="chart-wrap"><canvas id="rtChart"></canvas></div>
    </div>
    <div class="panel">
      <div class="panel-head"><div><h2>Log Terbaru</h2><div class="panel-subtitle">30 pemeriksaan terakhir</div></div></div>
      <div class="table-scroll"><table><thead><tr><th>Waktu</th><th>Situs</th><th>Kode</th><th>Response</th><th>Status</th></tr></thead><tbody id="logsBody"></tbody></table></div>
    </div>
  </div>

</div>

<div class="toast" id="toast"></div>

<script>
  const trendDownloadData = @json($dashboardTrend);
  function downloadTrendChart(){
    const canvas = document.createElement('canvas');
    const scale = 2;
    const width = 1200;
    const height = 650;
    canvas.width = width * scale;
    canvas.height = height * scale;
    const ctx = canvas.getContext('2d');
    ctx.scale(scale, scale);
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    ctx.fillStyle = '#17324d';
    ctx.font = '700 24px Arial';
    ctx.fillText('Tren Aktivitas Sistem', 52, 52);
    ctx.fillStyle = '#64748b';
    ctx.font = '14px Arial';
    ctx.fillText('Data aktivitas berdasarkan periode dashboard', 52, 78);
    const chart = { left: 72, top: 125, width: 1060, height: 380 };
    const colors = ['#dc2626', '#7c3aed', '#0891b2', '#f59e0b'];
    const keys = ['tickets', 'checklists', 'stock', 'licenses'];
    const labels = ['Tiket', 'Checklist', 'Stok', 'Lisensi'];
    const maxValue = Math.max(1, ...trendDownloadData.flatMap(item => keys.map(key => Number(item[key] || 0))));
    const roundedMax = Math.max(1, Math.ceil(maxValue / 5) * 5);
    ctx.font = '12px Arial';
    for(let step = 0; step <= 4; step++){
      const value = Math.round(roundedMax - roundedMax * step / 4);
      const y = chart.top + chart.height * step / 4;
      ctx.strokeStyle = '#e2e8f0';
      ctx.beginPath(); ctx.moveTo(chart.left, y); ctx.lineTo(chart.left + chart.width, y); ctx.stroke();
      ctx.fillStyle = '#64748b'; ctx.textAlign = 'right'; ctx.fillText(String(value), chart.left - 12, y + 4);
    }
    const groupWidth = chart.width / Math.max(1, trendDownloadData.length);
    const barWidth = Math.min(18, Math.max(8, groupWidth / 7));
    trendDownloadData.forEach((item, index) => {
      const groupLeft = chart.left + index * groupWidth;
      keys.forEach((key, keyIndex) => {
        const value = Number(item[key] || 0);
        const barHeight = value / roundedMax * chart.height;
        const x = groupLeft + groupWidth / 2 + (keyIndex - 1.5) * (barWidth + 5);
        const y = chart.top + chart.height - barHeight;
        ctx.fillStyle = colors[keyIndex];
        ctx.fillRect(x, y, barWidth, Math.max(2, barHeight));
        ctx.fillStyle = '#475569'; ctx.textAlign = 'center'; ctx.font = '700 11px Arial'; ctx.fillText(String(value), x + barWidth / 2, y - 7);
      });
      ctx.fillStyle = '#64748b'; ctx.font = '12px Arial'; ctx.fillText(item.label, groupLeft + groupWidth / 2, chart.top + chart.height + 25);
    });
    labels.forEach((label, index) => { const x = 72 + index * 110; ctx.fillStyle = colors[index]; ctx.fillRect(x, 570, 12, 12); ctx.fillStyle = '#475569'; ctx.textAlign = 'left'; ctx.font = '13px Arial'; ctx.fillText(label, x + 20, 581); });
    const link = document.createElement('a');
    link.download = 'tren-aktivitas-sistem.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
  }

  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
  let currentData = null;
  let selectedSiteId = null;

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
    document.getElementById('operationsWebStatus').textContent = data.summary.upSites + '/' + data.summary.totalSites;
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
    const canvas = document.getElementById('rtChart');
    if(!canvas) return;
    const label = document.getElementById('chartSiteLabel');
    const bounds = canvas.parentElement.getBoundingClientRect();
    const ratio = window.devicePixelRatio || 1;
    canvas.width = Math.max(1, Math.floor(bounds.width * ratio));
    canvas.height = Math.max(1, Math.floor(bounds.height * ratio));
    canvas.style.width = bounds.width + 'px'; canvas.style.height = bounds.height + 'px';
    const ctx = canvas.getContext('2d'); ctx.scale(ratio, ratio);
    const width = bounds.width, height = bounds.height;
    ctx.clearRect(0, 0, width, height);
    if(!site || !site.history.length){ label.textContent = site ? site.name + ' - belum ada data respons' : 'Pilih situs di atas'; ctx.fillStyle='#64748b'; ctx.font='13px sans-serif'; ctx.textAlign='center'; ctx.fillText('Belum ada data respons untuk ditampilkan', width / 2, height / 2); return; }
    const points = site.history.filter(h => Number.isFinite(Number(h.ms)));
    if(!points.length){ label.textContent = site.name + ' - belum ada data respons'; return; }
    label.textContent = site.name + ' | diperbarui ' + fmtTime(currentData.generatedAt);
    const pad = {top:20,right:16,bottom:30,left:48};
    const graphW = width - pad.left - pad.right, graphH = height - pad.top - pad.bottom;
    const max = Math.max(...points.map(point => Number(point.ms)), 1);
    const roundedMax = Math.ceil(max / 100) * 100 || 100;
    ctx.font='11px sans-serif'; ctx.fillStyle='#64748b'; ctx.textAlign='right';
    for(let step=0; step<=4; step++){
      const value = roundedMax - (roundedMax * step / 4); const y = pad.top + (graphH * step / 4);
      ctx.strokeStyle='#e2e8f0'; ctx.lineWidth=1; ctx.beginPath(); ctx.moveTo(pad.left,y); ctx.lineTo(width-pad.right,y); ctx.stroke();
      ctx.fillText(Math.round(value)+' ms', pad.left-7, y+4);
    }
    const xFor = index => pad.left + (points.length === 1 ? graphW / 2 : index * graphW / (points.length - 1));
    const yFor = value => pad.top + graphH - (Number(value) / roundedMax * graphH);
    const gradient = ctx.createLinearGradient(0,pad.top,0,pad.top+graphH); gradient.addColorStop(0,'rgba(11,94,168,.22)'); gradient.addColorStop(1,'rgba(11,94,168,0)');
    ctx.beginPath(); points.forEach((point,index) => { const x=xFor(index), y=yFor(point.ms); index ? ctx.lineTo(x,y) : ctx.moveTo(x,y); }); ctx.lineTo(xFor(points.length-1),pad.top+graphH); ctx.lineTo(xFor(0),pad.top+graphH); ctx.closePath(); ctx.fillStyle=gradient; ctx.fill();
    ctx.beginPath(); points.forEach((point,index) => { const x=xFor(index), y=yFor(point.ms); index ? ctx.lineTo(x,y) : ctx.moveTo(x,y); }); ctx.strokeStyle='#0b5ea8'; ctx.lineWidth=2.5; ctx.stroke();
    points.forEach((point,index) => { ctx.beginPath(); ctx.arc(xFor(index),yFor(point.ms),3.5,0,Math.PI*2); ctx.fillStyle=point.status === 'UP' ? '#0b5ea8' : '#dc2626'; ctx.fill(); });
    ctx.fillStyle='#64748b'; ctx.textAlign='left'; ctx.font='10px sans-serif'; const first=points[0], last=points[points.length-1]; ctx.fillText(fmtTime(first.t),pad.left,height-9); ctx.textAlign='right'; ctx.fillText(fmtTime(last.t),width-pad.right,height-9);
  }

  function renderLogs(logs){ const body = document.getElementById('logsBody'); if(!logs.length){ body.innerHTML = '<tr><td colspan="5" style="color:#8493A6">Belum ada log.</td></tr>'; return; } body.innerHTML = logs.map(l => '<tr><td>' + fmtDateTime(l.t) + '</td><td>' + escapeHtml(l.name) + '</td><td>' + (l.code || '-') + '</td><td>' + l.ms + ' ms</td><td class="status-' + l.status + '">' + l.status + '</td></tr>').join(''); }

  function escapeHtml(str){ const div = document.createElement('div'); div.textContent = str || ''; return div.innerHTML; }

  loadDashboard();
  window.addEventListener('resize', () => { if(currentData) renderChart(); });
  setInterval(loadDashboard, 15000);
</script>

@endsection
