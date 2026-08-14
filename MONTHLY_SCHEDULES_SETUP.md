# Fitur Jadwal Bulanan - Setup Panduan

## 📋 Ringkasan Perubahan

Sebuah sistem **Jadwal Bulanan** telah ditambahkan ke aplikasi. Sistem ini memungkinkan pengguna untuk membuat jadwal bulanan berdasarkan jadwal tahunan yang sudah dibuat, dengan memilih tanggal spesifik per peralatan dalam satu bulan.

## 📁 File-File yang Dibuat

### 1. Database Migration
- **File**: `database/migrations/2026_08_14_100000_create_monthly_schedules_table.php`
- **Tabel**: `monthly_schedules`
- **Kolom**:
  - `id` (Primary Key)
  - `checklist_item_id` (Foreign Key - Program Perawatan)
  - `equipment_id` (Foreign Key - Peralatan)
  - `month` (1-12)
  - `year` (tahun)
  - `dates` (JSON array - daftar tanggal terpilih)
  - `notes` (optional catatan)
  - `timestamps` (created_at, updated_at)
  - **Unique Constraint**: Per equipment per bulan (tidak boleh duplikat)

### 2. Model Eloquent
- **File**: `app/Models/MonthlySchedule.php`
- **Fitur**:
  - Casting array untuk kolom `dates`
  - Relationships: `belongsTo ChecklistItem`, `belongsTo Equipment`

### 3. Controller
- **File**: `app/Http/Controllers/MonthlyScheduleController.php`
- **Methods**:
  - `index()` - Daftar semua jadwal bulanan
  - `create()` - Form untuk memilih Program & Bulan
  - `edit($checklistItemId, $month, $year)` - Form untuk memilih tanggal per peralatan
  - `store(Request $request)` - Simpan jadwal bulanan
  - `show($id)` - Lihat detail jadwal
  - `update($id)` - Update jadwal
  - `destroy($id)` - Hapus jadwal
  - `getEquipmentByChecklist(Request $request)` - AJAX endpoint (optional)

### 4. Views (Blade Templates)
- **`resources/views/monthly_schedules/index.blade.php`**
  - Daftar semua jadwal bulanan dengan pagination
  - Tombol untuk membuat jadwal baru, lihat, dan hapus

- **`resources/views/monthly_schedules/create.blade.php`**
  - Form untuk memilih Program Perawatan
  - Pilih Bulan dan Tahun
  - Redirect ke form pemilihan tanggal

- **`resources/views/monthly_schedules/edit.blade.php`**
  - Menampilkan seluruh peralatan yang terkait dengan Program Perawatan
  - Checkbox untuk setiap tanggal (1-31) per peralatan
  - Styling dengan warna orange (#ff9800)
  - Tombol Simpan dan Kembali

- **`resources/views/monthly_schedules/show.blade.php`**
  - Tampilkan detail jadwal bulanan
  - Lihat tanggal-tanggal yang dipilih
  - Tombol untuk menghapus

### 5. Routes
Tambahan di `routes/web.php`:
```php
Route::get('/monthly-schedules', [MonthlyScheduleController::class, 'index'])->name('monthly_schedules.index');
Route::get('/monthly-schedules/create', [MonthlyScheduleController::class, 'create'])->name('monthly_schedules.create');
Route::post('/monthly-schedules', [MonthlyScheduleController::class, 'store'])->name('monthly_schedules.store');
Route::get('/monthly-schedules/{id}', [MonthlyScheduleController::class, 'show'])->name('monthly_schedules.show');
Route::get('/monthly-schedules/{checklistItemId}/{month}/{year}/edit', [MonthlyScheduleController::class, 'edit'])->name('monthly_schedules.edit');
Route::put('/monthly-schedules/{id}', [MonthlyScheduleController::class, 'update'])->name('monthly_schedules.update');
Route::delete('/monthly-schedules/{id}', [MonthlyScheduleController::class, 'destroy'])->name('monthly_schedules.destroy');
```

### 6. Model Relationships
- **Equipment Model**: Tambahan method `monthlySchedules()`
- **ChecklistItem Model**: Tambahan method `monthlySchedules()`

## 🚀 Langkah Setup

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Clear Cache & Views
```bash
php artisan view:clear
php artisan route:clear
php artisan cache:clear
```

### 3. Akses Fitur
Buka browser dan navigasi ke:
- **Daftar Jadwal Bulanan**: `/monthly-schedules`
- **Buat Jadwal Baru**: `/monthly-schedules/create`

## 📖 Alur Penggunaan

1. **Buat Jadwal Tahunan** (menggunakan fitur yang sudah ada)
   - Program Perawatan harus sudah terpilih
   - Equipment harus sudah dipilih
   - Jadwal tahunan dibuat dengan bulan, minggu, dan frekuensi

2. **Akses Menu Jadwal Bulanan**
   - Klik "Jadwal Bulanan" di menu (perlu ditambahkan ke navigasi)
   - Atau akses langsung: `/monthly-schedules`

3. **Buat Jadwal Bulanan Baru**
   - Klik tombol "Buat Jadwal Baru"
   - Pilih **Program Perawatan** (hanya program yang memiliki jadwal tahunan)
   - Pilih **Bulan** dan **Tahun**
   - Klik "Lanjut ke Pemilihan Tanggal"

4. **Pilih Tanggal per Peralatan**
   - Sistem akan menampilkan semua peralatan untuk Program yang dipilih
   - Untuk setiap peralatan, pilih tanggal-tanggal yang ingin dijadwalkan
   - Multiple selection support (bisa pilih banyak tanggal)
   - Klik "Simpan Jadwal Bulanan"

5. **Lihat atau Hapus Jadwal**
   - Di halaman daftar, klik ikon untuk melihat detail atau hapus

## 🎨 Styling
- Background color: Orange (#ff9800) - sesuai dengan tema aplikasi
- Checkbox styling: Orange accent dengan hover effects
- Responsive design: Mobile-friendly dengan grid layout

## 💾 Database Schema

### Tabel: `monthly_schedules`
```sql
CREATE TABLE monthly_schedules (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    checklist_item_id BIGINT NOT NULL FOREIGN KEY REFERENCES checklist_items(id) ON DELETE CASCADE,
    equipment_id BIGINT NOT NULL FOREIGN KEY REFERENCES equipment(id) ON DELETE CASCADE,
    month TINYINT NOT NULL,
    year SMALLINT NOT NULL,
    dates JSON NOT NULL DEFAULT '[]',
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_monthly_schedule (checklist_item_id, equipment_id, month, year)
);
```

## ⚠️ Catatan Penting

1. **Prasyarat**: Program Perawatan harus memiliki minimal 1 jadwal tahunan dengan peralatan terkait
2. **Unique Constraint**: Tidak boleh ada 2 jadwal untuk equipment yang sama, di bulan yang sama, di tahun yang sama
3. **JSON Array**: Kolom `dates` menyimpan array integer (contoh: `[1,5,10,15,20]`)
4. **Relationships**: Menghapus jadwal tahunan akan secara otomatis menghapus jadwal bulanan terkait

## 🔧 Integrasi Lebih Lanjut

Untuk integrasi lebih sempurna, tambahkan link ke menu/navigation:
```blade
<a href="{{ route('monthly_schedules.index') }}" class="nav-link">
    <i class="bi bi-calendar-check"></i> Jadwal Bulanan
</a>
```

## 📝 Fitur Tambahan (Opsional)

Untuk enhancement di masa depan:
- Integrasi dengan kalender (FullCalendar.js)
- Export ke PDF
- Notifikasi otomatis untuk maintenance yang akan datang
- Tracking status maintenance per tanggal
- Dashboard analytics
