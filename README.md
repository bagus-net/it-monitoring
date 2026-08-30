# IT Monitoring & Maintenance System

Sistem operasional internal PT Mulia Grand Manufacture untuk mengelola peralatan IT, monitoring website, perbaikan, jadwal perawatan, checklist, persediaan, dokumen ISO, inovasi, dan limbah IT.

## Teknologi

- PHP 8.1+
- Laravel 10
- MySQL atau MariaDB
- Bootstrap 5
- Vite
- Laragon direkomendasikan untuk pengembangan lokal Windows

## Fitur

### Aset dan Peralatan IT

- Data aset, tipe, manufacturer, lokasi, PIC, departemen, kondisi, dan kritikalitas.
- Detail aset berisi user pemilik, riwayat perawatan, mutasi, serta tiket perbaikan.
- Cetak label QR dan unduh label aset dalam format JPEG.
- Mutasi peralatan dengan proses persetujuan dan riwayat perpindahan.

### Tiket Perbaikan IT

- Tiket hardware dan software, prioritas, status, lampiran foto, tindakan perbaikan, dan persetujuan.
- Karyawan hanya melihat tiket serta peralatan yang dimilikinya.
- Pencarian cepat peralatan berdasarkan kode/inisial aset, nama peralatan, PIC, atau lokasi.
- Notifikasi polling untuk tiket baru dan tiket proses.

### Monitoring dan Perawatan

- Monitoring website dengan status layanan dan response time.
- Checklist Web Monitoring untuk pemeriksaan keamanan dan fungsional.
- Jadwal perawatan tahunan dan bulanan per program/peralatan.
- Cetak jadwal perawatan bulanan sebagai kalender tanggal.
- Pelaksanaan checklist dibandingkan dengan Jadwal Bulanan untuk menunjukkan kelengkapan peralatan dan pekerjaan yang belum diperiksa.
- Tanggal Jadwal tampil pada form buat, edit, detail checklist, dan riwayat perawatan aset.

### Persediaan dan Jaringan

- Stok tinta, sparepart, lisensi, dan CCTV.
- Riwayat stok masuk/keluar tinta.
- Topologi jaringan, node, link, dan zona.

### Inovasi IT

- Catat inovasi, implementasi, tanggal, dan keterangan.
- Unggah paper pendukung dalam format PDF, DOC, atau DOCX.

### Limbah IT dan Limbah B3

- Alur Box/Batch: buat box terlebih dahulu, kemudian tambah limbah harian ke batch tersebut.
- Kode box dan kode limbah dibuat otomatis.
- Mendukung botol tinta bekas, sisa tinta, limbah cleaning printer, cartridge/toner, baterai, kabel, dan komponen elektronik.
- Status box terbuka, siap diserahkan, atau sudah diserahkan ke Limbah B3.
- Cetak Berita Acara Serah Terima setelah batch berstatus sudah diserahkan ke Limbah B3.

### Dokumen ISO

- Ruang berbagi dokumen internal dengan izin pengguna per dokumen.
- Master/Admin IT memilih penerima; penerima hanya dapat melihat serta mengunduh dokumen yang dibagikan kepadanya.
- File disimpan privat dan dilindungi pemeriksaan akses server.
- Mendukung PDF, Word, Excel, dan PowerPoint hingga 20 MB.
- Preview inline untuk PDF, XLS, dan XLSX.
- Nomor dokumen otomatis, kategori, revisi, tanggal, dan deskripsi.

### User dan Keamanan

- Peran `Master`, `Admin IT`, dan `User / Karyawan`.
- Profil mandiri untuk mengganti foto profil serta password.
- Avatar default perusahaan jika belum ada foto profil.
- Detail user menampilkan peralatan yang ditugaskan dan Master dapat melepaskan aset dari user.
- Privacy Policy internal.

### Sampah Data

Soft delete diterapkan pada Peralatan IT, User, Checklist Perawatan, Inovasi IT, Dokumen ISO, Limbah IT, dan Box Limbah.

- Data terhapus masuk ke `Pengaturan > Trash`.
- Master dapat memulihkan data atau menghapusnya permanen.
- File foto, paper, dan dokumen hanya dihapus pada penghapusan permanen.

## Hak Akses

| Peran | Akses utama |
| --- | --- |
| Master | Seluruh fitur, pengaturan user, jadwal, persetujuan, log aktivitas, dan Trash. |
| Admin IT | Operasional IT, aset, tiket, checklist, stok, inovasi, limbah, dan Dokumen ISO. |
| User / Karyawan | Tiket/peralatan sendiri, Dokumen ISO yang dibagikan, profil, dan tanda tangan digital. |

## Instalasi Lokal

1. Siapkan PHP 8.1+, Composer, Node.js, dan MySQL/MariaDB.
2. Buat file environment:

```powershell
Copy-Item .env.example .env
```

1. Atur koneksi database pada `.env`:

```env

1. Instal dependensi dan siapkan aplikasi:

```powershell
composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan migrate
```

1. Jalankan aplikasi:

```powershell
php artisan serve
npm run dev
```

Untuk Laragon, aplikasi dapat dibuka melalui virtual host lokal, misalnya `http://it-monitoring.test`.

## Scheduler Monitoring

Di server, jalankan Laravel Scheduler setiap menit:

```text
* * * * * cd /path/ke/it-monitoring && php artisan schedule:run >> /dev/null 2>&1
```

Untuk pengembangan lokal:

```powershell
php artisan schedule:work
```

## Pengujian

```powershell
php artisan test
```

Database test harus berbeda dari database pengembangan. Konfigurasi PHPUnit proyek menggunakan database MySQL khusus `it_health_testing`; jangan menjalankan test yang memakai `RefreshDatabase` terhadap database aktif.

## Struktur Penting

```text
app/Http/Controllers/     Controller aplikasi
app/Models/               Model Eloquent
app/Services/             Site monitor dan notifikasi WhatsApp
database/migrations/      Struktur database
resources/views/          Blade views
routes/web.php            Rute web
public/images/            Logo MGM dan avatar default
```

## Keamanan

- Dokumen ISO tersimpan privat dan endpoint preview/unduh selalu memeriksa izin pengguna.
- Password memakai hashing bawaan Laravel.
- Soft delete memberi kesempatan pemulihan sebelum data serta file dihapus permanen.

## Identitas

PT Mulia Grand Manufacture

IT Monitoring & Maintenance System

Dibuat oleh ITMGM 2026.
