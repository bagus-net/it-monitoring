# IT Monitoring Dashboard — Laravel 8

Sistem monitoring website (status UP/DOWN + response time) berbasis Laravel 8, dengan pengecekan terjadwal via scheduler dan dashboard real-time (AJAX + Chart.js).

## Struktur file yang disertakan

```
app/Console/Commands/CheckSites.php
app/Console/Kernel.php
app/Http/Controllers/DashboardController.php
app/Http/Controllers/SiteController.php
app/Models/Site.php
app/Models/MonitoringLog.php
app/Services/SiteMonitorService.php
database/migrations/2026_08_13_000001_create_sites_table.php
database/migrations/2026_08_13_000002_create_monitoring_logs_table.php
resources/views/dashboard.blade.php
routes/web.php
```

Semua file di atas didesain untuk **ditempel ke dalam project Laravel 8 yang sudah ada**, bukan project yang berdiri sendiri — jadi kamu perlu membuat project Laravel kosong dulu.

## 1. Buat project Laravel 8

```bash
composer create-project laravel/laravel:^8.54 it-monitoring
cd it-monitoring
```

> Catatan: gunakan `^8.54` (bukan sekadar `^8.0`) kalau kamu menjalankan **PHP 8.1**, karena dukungan resmi PHP 8.1 baru masuk di Laravel 8.54. Cek versi PHP-mu dengan `php -v` dulu.

## 2. Salin file dari paket ini

Salin seluruh isi folder `app/`, `database/`, `resources/`, dan `routes/` dari paket ini ke root project Laravel-mu, **timpa file yang sudah ada** (khususnya `app/Console/Kernel.php` dan `routes/web.php`, karena keduanya sudah diisi lengkap termasuk kode bawaan yang perlu ada).

## 3. Konfigurasi database

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_monitoring
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `it_monitoring` di MySQL/MariaDB-mu (atau ganti ke `sqlite` kalau mau lebih ringan — cukup `DB_CONNECTION=sqlite` dan buat file `database/database.sqlite` kosong).

## 4. Migrasi & jalankan

```bash
php artisan migrate
php artisan serve
```

Buka `http://127.0.0.1:8000` — dashboard langsung tampil. Tambahkan URL yang ingin dipantau lewat form di halaman, lalu klik **Cek Sekarang** untuk pengecekan pertama.

## 5. Aktifkan pengecekan otomatis (scheduler)

Laravel scheduler butuh satu entri cron di server (bukan banyak cron per-tugas):

```
* * * * * cd /path/ke/project && php artisan schedule:run >> /dev/null 2>&1
```

Tambahkan baris itu ke crontab server (`crontab -e`). Setelah itu, `monitor:check` akan berjalan otomatis setiap 5 menit sesuai jadwal di `app/Console/Kernel.php` (bisa diubah ke `everyMinute()`, `everyTenMinutes()`, dst).

Untuk development lokal tanpa cron, kamu bisa jalankan manual di terminal terpisah:

```bash
php artisan schedule:work
```

## Catatan teknis & batasan

- Response time diukur dari server tempat Laravel berjalan (bukan dari browser pengguna), jadi cocok untuk cek ketersediaan, bukan pengukuran latensi sisi pengguna akhir.
- Situs yang butuh login atau memblokir request non-browser bisa terbaca "DOWN" meski sebenarnya online — batasan umum monitoring berbasis HTTP client.
- `SiteMonitorService::check()` memakai `Http::timeout(10)` — naikkan kalau kamu memantau situs yang lambat tapi valid.
- Untuk performa lebih baik saat jumlah situs banyak, jalankan `monitor:check` di queue (`Http::async()` atau dispatch job per-situs) alih-alih sinkron — bisa saya bantu ubah kalau perlu.

## Ide pengembangan lanjutan

- Notifikasi email/Slack/Telegram saat status berubah jadi DOWN (tambahkan `Notification`/`Mail` di `SiteMonitorService::check()`).
- Autentikasi (Laravel Breeze/Jetstream) supaya dashboard tidak publik.
- Threshold response time untuk menandai situs "lambat".
- Laporan uptime mingguan/bulanan (export PDF/Excel).
- Multi-user dengan grup situs per tim.
