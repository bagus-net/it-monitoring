@php
    $documentQuotes = config('daily_quotes');
    $documentQuote = $documentQuotes[((int) now()->format('z')) % count($documentQuotes)];
@endphp

<div class="document-header-banner">
    <div class="document-header-quote">
        <div class="document-header-quote-icon"><i class="bi bi-quote"></i></div>
        <div>
            <div class="document-header-kicker"><i class="bi bi-brightness-high-fill"></i> Quote of the Day</div>
            <div class="document-header-quote-text">&quot;{{ $documentQuote['text'] }}&quot;</div>
            <div class="document-header-author">— {{ $documentQuote['author'] }}</div>
        </div>
    </div>
    <div class="document-header-meta">
        <span class="document-header-widget" id="documentWeatherWidget"><i class="bi bi-cloud-sun text-warning"></i> <span id="documentWeatherText">Memuat Cuaca...</span></span>
        <span class="document-header-divider">|</span>
        <span class="document-header-widget" id="documentUsdWidget"><i class="bi bi-currency-dollar text-success"></i> <span id="documentUsdText">USD Memuat...</span></span>
        <span class="document-header-divider">|</span>
        <span class="document-header-widget"><i class="bi bi-currency-bitcoin text-warning"></i> <span id="documentCryptoText">Crypto memuat...</span></span>
        <span class="document-header-divider">|</span>
        <span class="document-header-widget"><i class="bi bi-gem text-warning"></i> <span id="documentGoldText">ANTAM memuat...</span></span>
        <span class="document-header-divider">|</span>
        <span class="document-header-widget"><i class="bi bi-shield-check text-warning"></i> Hak Akses: {{ auth()->user()->roleLabel() }}</span>
    </div>
</div>

<style>
.document-header-banner{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 22px;padding:15px 22px;border-radius:16px;background:linear-gradient(135deg,#075985 0%,#0369a1 42%,#0f766e 100%);color:#fff;box-shadow:0 10px 25px rgba(7,89,133,.18);overflow:hidden;position:relative}.document-header-banner:before{content:'';position:absolute;right:-42px;top:-62px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.08)}.document-header-quote,.document-header-meta{position:relative;z-index:1}.document-header-quote{display:flex;align-items:center;gap:14px;min-width:0}.document-header-quote-icon{display:grid;place-items:center;width:44px;height:44px;flex:0 0 44px;border:1px solid rgba(255,255,255,.25);border-radius:12px;background:rgba(255,255,255,.18);color:#fef08a;font-size:1.35rem}.document-header-kicker{color:#bae6fd;font-size:.65rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.document-header-quote-text{font-size:.94rem;font-weight:700;font-style:italic;line-height:1.35}.document-header-author{color:#7dd3fc;font-size:.74rem;font-weight:600}.document-header-meta{display:flex;align-items:center;gap:9px;flex-shrink:0;max-width:68%;padding:7px 13px;border:1px solid rgba(255,255,255,.22);border-radius:999px;background:rgba(255,255,255,.14);font-size:.72rem;font-weight:700;white-space:nowrap;overflow:hidden}.document-header-widget{display:inline-flex;align-items:center;gap:4px}.document-header-widget span{overflow:hidden;text-overflow:ellipsis}.document-header-meta i{margin-right:2px}.document-header-divider{opacity:.35}@media(max-width:700px){.document-header-banner{align-items:stretch;flex-direction:column;gap:12px;padding:14px 16px;border-radius:14px}.document-header-meta{justify-content:center;max-width:none;white-space:normal;flex-wrap:wrap}.document-header-quote-text{font-size:.84rem}}
@media print{.document-header-banner{display:none!important}}
</style>

<script>
    (() => {
        const weatherText = document.getElementById('documentWeatherText');
        const usdText = document.getElementById('documentUsdText');
        const cryptoText = document.getElementById('documentCryptoText');
        const goldText = document.getElementById('documentGoldText');

        fetch('https://api.open-meteo.com/v1/forecast?latitude=-7.1568&longitude=112.6555&current_weather=true')
            .then(response => response.json())
            .then(data => {
                const weather = data.current_weather;
                const descriptions = { 0: 'Cerah', 1: 'Cerah Berawan', 2: 'Berawan', 3: 'Mendung', 45: 'Berkabut', 51: 'Gerimis', 61: 'Hujan', 80: 'Hujan Lokal', 95: 'Badai' };
                if (weather && weatherText) weatherText.textContent = `${Math.round(weather.temperature)}°C ${descriptions[weather.weathercode] || 'Berawan'}`;
            }).catch(() => { if (weatherText) weatherText.textContent = 'Cuaca tidak tersedia'; });

        fetch('https://open.er-api.com/v6/latest/USD')
            .then(response => response.json())
            .then(data => {
                if (!data.rates?.IDR || !usdText) throw new Error('USD unavailable');
                const rate = new Intl.NumberFormat('id-ID').format(Math.round(Number(data.rates.IDR)));
                const date = data.time_last_update_utc ? new Date(data.time_last_update_utc).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : 'terbaru';
                usdText.innerHTML = `USD Rp ${rate} <small>(${date})</small>`;
            }).catch(() => { if (usdText) usdText.textContent = 'USD tidak tersedia'; });

        const fetchCryptoPrices = () => fetch('{{ route('dashboard.crypto') }}')
            .then(response => {
                if (!response.ok) throw new Error('Harga crypto tidak tersedia');
                return response.json();
            })
            .then(data => {
                if (!cryptoText) return;
                const format = value => value == null ? '-' : new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: value >= 100 ? 0 : 2 }).format(value);
                const change = value => value == null ? '' : `${Number(value) >= 0 ? '+' : ''}${Number(value).toFixed(2)}%`;
                const bitcoin = data.prices?.bitcoin;
                const ethereum = data.prices?.ethereum;
                cryptoText.innerHTML = `BTC ${format(bitcoin?.usd)}<small class="crypto-change">${change(bitcoin?.change24h)}</small> · ETH ${format(ethereum?.usd)}<small class="crypto-change">${change(ethereum?.change24h)}</small>`;
            })
            .catch(() => { if (cryptoText) cryptoText.textContent = 'Crypto tidak tersedia'; });
        fetchCryptoPrices();
        setInterval(fetchCryptoPrices, 30000);

        const fetchGoldPrice = () => fetch('{{ route('dashboard.gold') }}')
            .then(response => {
                if (!response.ok) throw new Error('Harga gold tidak tersedia');
                return response.json();
            })
            .then(data => {
                if (!goldText || data.price == null) throw new Error('Gold unavailable');
                const updatedAt = data.updatedAt ? new Date(data.updatedAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
                goldText.innerHTML = `ANTAM 1g ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(data.price)}<small class="crypto-change">${data.isFallback ? 'terakhir' : updatedAt}</small>`;
            })
            .catch(() => { if (goldText) goldText.textContent = 'ANTAM tidak tersedia'; });
        fetchGoldPrice();
        setInterval(fetchGoldPrice, 60000);
    })();
</script>
