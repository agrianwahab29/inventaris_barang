# AGENTS.md - Sistem Inventaris Kantor

## Project Snapshot

**Repository**: https://github.com/agrianwahab29/inventaris_barang.git  
**Tech Stack**: Laravel 8.x, PHP 8.0+, MySQL, Bootstrap 5, JavaScript, Chart.js  
**Type**: Single Laravel Application (Monolithic)  
**Purpose**: Sistem manajemen inventaris barang kantor dengan tracking transaksi, stok opname, arsip dokumen digital, dan surat tanda terima  
**Status**: ✅ **Production Ready** - Responsive design mobile-first (320px - desktop), SQA audit passed  
**Latest Commit**: `0701c0f` - fix: dashboard UI/UX improvements (color palette, typography, chart visibility, alert highlighting)

---

## 🎨 Design System

### Login Page - Professional Government Theme

Halaman login didesain dengan tema **profesional pemerintahan** yang bersih dan elegan untuk Balai Bahasa Provinsi Sulawesi Tenggara:

| Aspek | Implementasi |
|-------|--------------|
| **Background** | Light gradient `#f8fafc` → `#f1f5f9` (cerah dan bersih) |
| **Primary Color** | Blue `#1e4d8c` (institusional) |
| **Accent Color** | Gold `#c9a227` (elegan) |
| **Logo** | Single logo Kemendikdasmen (100px) |
| **Card** | White background dengan subtle shadow |
| **Typography** | Georgia serif untuk heading, System UI untuk body |
| **Button** | Blue solid dengan hover effect |
| **Header** | "Sistem Inventaris Barang Balai Bahasa Provinsi Sulawesi Tenggara" |
| **Subtitle** | "Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi" |

**Karakteristik Desain:**
- ✅ **Clean & Minimal** - desain yang tidak terkesan AI
- ✅ **Profesional institusional** - cocok untuk pemerintahan
- ✅ **Single logo** - Kemendikdasmen saja, posisi terpusat
- ✅ **Typography elegan** - serif untuk heading
- ✅ **No security badge** - dihapus sesuai permintaan
- ✅ **No "Lupa Password"** - sesuai permintaan
- ✅ **Fully responsive** (mobile-first)

**Institusi:**
- **Nama**: Balai Bahasa Provinsi Sulawesi Tenggara
- **Induk**: Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi

**File**: `resources/views/auth/login.blade.php`

---

### Dashboard - Modern Admin Interface

Dashboard didesain dengan **modern admin interface** yang informatif dan user-friendly:

| Aspek | Implementasi |
|-------|--------------|
| **Welcome Banner** | Blue gradient `#1e4d8c` dengan animasi gradient flow |
| **Stat Cards** | 4-column grid dengan icons dan badges |
| **Color Palette** | Simplified - 4 warna utama: Blue, Green, Orange, Red |
| **Chart** | Chart.js dengan opacity 0.25 (area fill lebih visible) |
| **Typography** | Consistent - semua label menggunakan font-weight 500 |
| **Alert Cards** | "Perlu Perhatian" dengan pulse animation dan shadow |
| **Active Menu** | Dashboard menu highlighted di sidebar |
| **Empty States** | Empty state message untuk chart tanpa data |

**Color Palette (4 warna):**
- **Primary**: Blue `#1e4d8c` (institusional)
- **Success**: Green `#10b981` (positive metrics)
- **Warning**: Orange `#f59e0b` (attention needed)
- **Danger**: Red `#ef4444` (alerts & warnings)

**Recent Improvements:**
- ✅ Chart opacity ditingkatkan (0.1 → 0.25)
- ✅ Typography consistency (all labels font-weight 500)
- ✅ Color palette simplified (purple → blue institutional)
- ✅ Alert card dengan pulse animation
- ✅ Active menu state di sidebar
- ✅ Empty state untuk chart

**File**: `resources/views/dashboard/index.blade.php`

---

## 📋 Fitur Utama

| Modul | Status | Deskripsi |
|-------|--------|-----------|
| **Auth** | ✅ | Login/Logout dengan role-based access (admin/pengguna) |
| **Dashboard** | ✅ | Statistik, chart, quick actions, stock alerts, recent transactions |
| **Barang** | ✅ | CRUD barang dengan tracking stok, filter, export Excel |
| **Ruangan** | ✅ | CRUD ruangan, bulk select, filter |
| **Transaksi** | ✅ | CRUD transaksi masuk/keluar, auto-update stok, export Excel |
| **Quarterly Stock** | ✅ | Stok opname per kuartal, filter tahun, export DOCX |
| **Surat Tanda Terima** | ✅ | Grouped by tanggal, filter, cetak DOCX |
| **Berkas Transaksi** | ✅ | Arsip dokumen digital (PDF), upload/preview/search, card/list view |
| **Users** | ✅ | CRUD user, role management, card/list view |

---

## 📱 Responsive Design

Sistem sudah **fully responsive** dengan pendekatan mobile-first:

### Breakpoints
- **320px** (iPhone SE) - Compact layout
- **375px** (iPhone) - Standard mobile
- **575.98px** - Small mobile adjustments
- **767.98px** - Tablet adjustments
- **1024px+** - Desktop full layout

### Fitur Responsive
- ✅ Sidebar toggle dengan overlay (mobile)
- ✅ Stat cards grid 2-per-row di mobile
- ✅ Table horizontal scroll dengan `min-width`
- ✅ Form stacking (col-12) di mobile
- ✅ Filter form responsive
- ✅ iOS zoom fix (font-size 16px pada input)
- ✅ Date field shortening di mobile
- ✅ Card view toggle untuk list data
- ✅ Export modal fullscreen di mobile

---

## 🐛 Bug Fixes Applied

| Bug | Fix |
|-----|-----|
| **Duplicate toast message** | Hapus duplicate alert di transaksi index, pertahankan hanya di layout |
| **tanggal_keluar default value** | Empty string default + JS auto-update saat jumlah_keluar > 0 |
| **Role CSS mismatch** | Fix `.user` → `.pengguna` untuk avatar/badge |
| **Stats dari pagination** | Controller passing `$stats` terpisah (bukan dari paginated collection) |
| **iOS zoom on input** | `font-size: 16px` pada form controls di mobile breakpoint |
| **Bad transaction data** | Clean transaction 2010 dengan tanggal_keluar NULL |

---

## 🔐 Security & Compliance

- ✅ Role-based access control (admin/pengguna)
- ✅ CSRF protection (Laravel default)
- ✅ XSS prevention (Blade templating)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Password hashing (bcrypt)
- ✅ Policy-based authorization (BerkasTransaksiPolicy)
- ✅ `.env` excluded from git
- ✅ No default credentials in login page
- ✅ SQA audit completed - all features tested

---

## 📂 File Structure

```
inventaris-kantor/
├── app/
│   ├── Console/Commands/          ← Artisan commands
│   │   ├── FixStock.php
│   │   ├── ImportCSVTransaksi.php
│   │   ├── ImportTransaksiCsv.php
│   │   └── UpdateStokBarang.php
│   ├── Exports/                   ← Excel export classes
│   │   └── BarangExport.php
│   ├── Http/
│   │   ├── Controllers/           ← Controllers (9 files)
│   │   │   ├── AuthController.php
│   │   │   ├── BarangController.php
│   │   │   ├── BerkasTransaksiController.php
│   │   │   ├── Controller.php
│   │   │   ├── DashboardController.php
│   │   │   ├── QuarterlyStockController.php
│   │   │   ├── RuanganController.php
│   │   │   ├── SuratTandaTerimaController.php
│   │   │   └── TransaksiController.php
│   │   └── Middleware/            ← Middleware (8 files)
│   ├── Models/                    ← Eloquent models (6 files)
│   │   ├── Barang.php
│   │   ├── BerkasTransaksi.php
│   │   ├── QuarterlyStockOpname.php
│   │   ├── Ruangan.php
│   │   ├── Transaksi.php
│   │   └── User.php
│   ├── Policies/                  ← Authorization policies
│   │   └── BerkasTransaksiPolicy.php
│   └── Providers/                 ← Service providers
├── config/                        ← Configuration files
├── database/
│   ├── migrations/                ← Database migrations (9 files)
│   └── seeders/                   ← Database seeders
├── docs/                          ← Documentation
├── dummy/                         ← Dummy data files (100 PDFs)
├── public/                        ← Public assets
│   └── image/                     ← Images (logo, favicon)
├── qa_test/                       ← QA test screenshots & reports
├── resources/
│   ├── views/                     ← Blade templates
│   │   ├── auth/                  ← Login page
│   │   ├── barang/                ← Barang CRUD views
│   │   ├── berkas-transaksi/      ← Berkas Transaksi views
│   │   ├── dashboard/             ← Dashboard view
│   │   ├── layouts/               ← Main layout (responsive CSS)
│   │   ├── pagination/            ← Pagination partials
│   │   ├── quarterly-stock/       ← Stock opname views
│   │   ├── ruangan/               ← Ruangan CRUD views
│   │   ├── surat-tanda-terima/    ← Surat tanda terima views
│   │   ├── transaksi/             ← Transaksi CRUD views
│   │   ├── users/                 ← User management views
│   │   └── vendor/                ← Vendor pagination views
│   ├── js/                        ← JavaScript files
│   └── css/                       ← CSS files
├── routes/
│   └── web.php                    ← Web routes (role-based middleware)
├── scripts/                       ← Custom scripts (auto-backup)
├── storage/                       ← Storage (logs, cache, uploads)
├── tests/                         ← Unit & Feature tests
├── .env.example                   ← Environment template
├── composer.json                  ← PHP dependencies
└── README.md                      ← Project documentation
```

---

## 🚀 Deployment Structure

```
deploy_separated/
├── upload_public_html/            ← Files for web root (public_html)
│   ├── image/                     ← Logo, favicon
│   ├── index.php                  ← Laravel public entry
│   ├── .htaccess                  ← Apache config
│   ├── web.config                 ← IIS config
│   ├── robots.txt
│   ├── aktifkan-admin.php         ← Admin activation script
│   ├── clear-cache.php            ← Cache clearing script
│   ├── jalankan-migrasi.php       ← Migration runner script
│   └── upload_public_html.zip     ← Deployment archive
├── upload_root/                   ← Files for root (outside public_html)
│   ├── app/                       ← Application code
│   ├── bootstrap/                 ← Bootstrap files
│   ├── config/                    ← Configuration
│   ├── database/                  ← Migrations & seeders
│   ├── public/                    ← Public assets
│   ├── resources/                 ← Views & assets
│   ├── routes/                    ← Web routes
│   ├── storage/                   ← Storage (logs, cache)
│   ├── vendor/                    ← Dependencies
│   ├── .env                       ← Environment config
│   ├── artisan                    ← CLI entry
│   ├── composer.json              ← PHP dependencies
│   ├── composer.lock              ← Locked dependencies
│   └── upload_root.zip            ← Deployment archive
├── SQA_FINAL_REPORT.md            ← Quality assurance report
├── PANDUAN_DEPLOY.md              ← Deployment guide
├── README.md                      ← Project readme
├── CHANGELOG.txt                  ← Change history
└── INFO.txt                       ← Project info
```

---

## 🔄 Sync Workflow

### Development → Deployment

```bash
# 1. Commit & push ke GitHub
git add .
git commit -m "feat: deskripsi perubahan"
git push origin master

# 2. Sync ke deploy_separated
# Sync app folder
robocopy "inventaris-kantor\app" "deploy_separated\upload_root\app" /MIR /E
# Sync views
robocopy "inventaris-kantor\resources\views" "deploy_separated\upload_root\resources\views" /MIR /E
# Sync migrations
robocopy "inventaris-kantor\database\migrations" "deploy_separated\upload_root\database\migrations" /E
# Sync routes
Copy-Item "inventaris-kantor\routes\web.php" "deploy_separated\upload_root\routes\web.php" -Force
# Sync public images
robocopy "inventaris-kantor\public\image" "deploy_separated\upload_public_html\image" /MIR /E
```

---

## 🧪 Testing & QA

### SQA Audit Coverage

| Area | Status | Details |
|------|--------|---------|
| Login | ✅ | Wrong credentials error, successful redirect |
| Dashboard | ✅ | Stats, chart, quick actions, stock alerts, recent transactions |
| Barang | ✅ | 22 items render, filters, checkboxes, action buttons |
| Ruangan | ✅ | 2 rooms render, checkboxes, action buttons |
| Transaksi Index | ✅ | All transactions render, filter "Keluar" works |
| Transaksi Create | ✅ | Form validation, barang selection, stock info dynamic |
| Transaksi Edit | ✅ | Form loads with correct data |
| Transaksi Delete | ✅ | Delete works, stock rollback verified |
| Transaksi Show | ✅ | Detail data correct |
| Quarterly Stock | ✅ | Q2 2026 data, year/quarter filter, export DOCX |
| Surat Tanda Terima | ✅ | Groups render, pengambil/date filter, cetak DOCX |
| Berkas Transaksi | ✅ | 100 docs, pagination, search/date/year/uploader filters |
| Users | ✅ | Stats correct, status filter, card/list view toggle |
| Responsive Design | ✅ | 320px - desktop tested, iOS zoom fix |
| Role-based Access | ✅ | Admin vs pengguna routes with middleware |

### Test URLs
```
http://127.0.0.1:8000/login
http://127.0.0.1:8000/dashboard
http://127.0.0.1:8000/barang
http://127.0.0.1:8000/ruangan
http://127.0.0.1:8000/transaksi/create
http://127.0.0.1:8000/transaksi
http://127.0.0.1:8000/quarterly-stock
http://127.0.0.1:8000/surat-tanda-terima
http://127.0.0.1:8000/berkas-transaksi
http://127.0.0.1:8000/users
```

### Test Credentials
- **Admin**: `admin` / `admin123` (role: admin)
- **User**: `user` / `user123` (role: pengguna)

---

## 📝 Key Patterns

### Controllers (app/Http/Controllers/)

**Pattern:**
- Resource controllers untuk CRUD (Barang, Ruangan, Users)
- Custom controllers untuk fitur khusus (Dashboard, Transaksi, Quarterly Stock)
- Role-based middleware untuk admin-only routes

**Contoh:**
```php
// app/Http/Controllers/BarangController.php
public function index()
{
    $barangs = Barang::with('ruangan')->paginate(10);
    return view('barang.index', compact('barangs'));
}
```

### Models (app/Models/)

**Pattern:**
- Eloquent relationships (belongsTo, hasMany)
- Fillable fields protection
- Query scopes untuk reusable queries
- Accessors & mutators untuk data transformation

**Contoh:**
```php
// app/Models/Barang.php
class Barang extends Model
{
    protected $fillable = ['kode', 'nama', 'satuan', 'stok', 'ruangan_id'];
    
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
```

### Views (resources/views/)

**Pattern:**
- Blade templating dengan `@extends('layouts.app')`
- Responsive CSS dengan 3-tier breakpoints
- Card view & list view toggle untuk data display
- Bootstrap 5 grid system dengan mobile-first approach

**Contoh:**
```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Barang</h1>
        @include('barang._table', ['barangs' => $barangs])
    </div>
@endsection
```

### Policies (app/Policies/)

**Pattern:**
- Authorization policies untuk resource access control
- Gate-based policy registration
- Role-based policy checks

**Contoh:**
```php
// app/Policies/BerkasTransaksiPolicy.php
class BerkasTransaksiPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin' || $user->role === 'pengguna';
    }
}
```

---

## ⚙️ Commands Reference

### Development

```bash
# Install dependencies
composer install
npm install

# Run development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Git Operations

```bash
# Status
git status

# Add files
git add .
git add nama-file.php

# Commit
git commit -m "pesan commit"

# Push
git push origin master

# Pull
git pull origin master

# View log
git log --oneline -10

# View diff
git diff

# Stash changes
git stash
git stash pop
```

### Export/Import

```bash
# Export data transaksi
php artisan export:transaksi

# Import CSV
php artisan import:csv-transaksi

# Update stok
php artisan update:stok-barang
```

---

## 🔒 Security & Best Practices

### Jangan Commit

- ❌ `.env` (sudah di .gitignore)
- ❌ `vendor/` (sudah di .gitignore)
- ❌ `node_modules/` (sudah di .gitignore)
- ❌ File database (`.sql`, `.sqlite`)
- ❌ File upload user (kecuali dummy)
- ❌ API keys, passwords, tokens
- ❌ Default credentials di login page

### Selalu Commit

- ✅ Migration files
- ✅ Seeder files
- ✅ Configuration files (config/)
- ✅ Documentation (docs/)
- ✅ Scripts (scripts/)
- ✅ Policy files (app/Policies/)

### Backup Rutin

**Manual:**
```bash
./scripts/auto-backup.sh
```

**Atau setup cron job (Linux/Mac):**
```bash
# Edit crontab
crontab -e

# Tambahkan (backup setiap jam)
0 * * * * cd /path/to/inventaris-kantor && ./scripts/auto-backup.sh
```

---

## 🐛 Troubleshooting

### Masalah Push

**Error: "rejected non-fast-forward"**
```bash
git pull origin master --rebase
git push origin master
```

**Error: "Permission denied"**
- Cek remote URL: `git remote -v`
- Pastikan punya akses ke repository

### Masalah Merge

**Conflict saat pull:**
```bash
# Lihat file yang conflict
git status

# Edit file conflict, lalu
git add .
git commit -m "resolve merge conflict"
```

### Recovery File

**File terhapus tapi sudah di-commit:**
```bash
# Lihat history file
git log --follow -- nama-file.php

# Restore file
git checkout <commit-hash> -- nama-file.php
```

### View Cache Issues

**Blade changes not reflecting:**
```bash
php artisan view:clear
php artisan cache:clear
```

### LSP False Positives

Blade files sering memicu false-positive errors dari LSP karena campuran PHP & JS. Gunakan `php -l` untuk verifikasi sebenarnya:
```bash
php -l resources/views/barang/index.blade.php
```

### Disk Space Issues

Laravel log file bisa penuh dan menyebabkan error. Truncate dengan:
```bash
# Windows PowerShell
"" | Out-File -FilePath "storage/logs/laravel.log" -Encoding utf8

# Linux/Mac
> storage/logs/laravel.log
```

---

## ✅ Definition of Done

Sebelum push ke GitHub, pastikan:

- [ ] Kode berjalan tanpa error
- [ ] Tidak ada file sensitif (.env, credentials)
- [ ] Commit message jelas
- [ ] Sudah di-test (jika ada fitur baru)
- [ ] Documentation updated (jika perlu)
- [ ] Responsive design tested (mobile + desktop)
- [ ] No duplicate toast/alert messages
- [ ] View cache cleared setelah edit blade
- [ ] LSP checks passed (atau false positive confirmed)

---

## 📋 Quick Commands Cheat Sheet

```bash
# Setup baru
git clone https://github.com/agrianwahab29/inventaris_barang.git
cd inventaris_barang
cp .env.example .env
composer install
php artisan key:generate

# Daily workflow
git pull origin master
# ... edit files ...
git add .
git commit -m "feat: deskripsi"
git push origin master

# Emergency rollback
git log --oneline -5
git reset --hard <commit-hash>
git push origin master --force  # Hati-hati!

# Backup otomatis
./scripts/auto-backup.sh

# Clear cache setelah edit blade
php artisan view:clear
php artisan cache:clear
```

---

## 🔗 Links & Resources

- **Repository**: https://github.com/agrianwahab29/inventaris_barang
- **Commits History**: https://github.com/agrianwahab29/inventaris_barang/commits/master
- **Issues**: https://github.com/agrianwahab29/inventaris_barang/issues
- **Laravel Docs**: https://laravel.com/docs/8.x
- **Bootstrap 5 Docs**: https://getbootstrap.com/docs/5.0/getting-started/introduction/

---

## 📊 Project Status

| Metric | Value |
|--------|-------|
| **Completion** | ✅ 100% Production Ready |
| **Responsive Design** | ✅ Mobile-first (320px - desktop) |
| **SQA Audit** | ✅ All features tested & passed |
| **Bug Fixes** | ✅ All known bugs resolved |
| **Documentation** | ✅ Complete & up-to-date |
| **Deployment** | ✅ Ready for hosting |
| **Security** | ✅ Role-based access, CSRF, XSS prevention |
| **Code Quality** | ✅ LSP checks passed, no syntax errors |

---

## 📝 Catatan Penting

> **Auto-push sudah aktif!** Setiap kali Anda commit, kode otomatis tersimpan di GitHub. Jika terjadi kesalahan, Anda selalu bisa mengambil kode dari commit sebelumnya melalui GitHub atau menggunakan `git checkout`.

> **Selalu pull sebelum push** untuk menghindari conflict.

> **Gunakan commit message yang jelas** agar mudah tracking perubahan.

> **Clear view cache** setelah edit blade files: `php artisan view:clear`

> **LSP false positives** pada blade files adalah normal - gunakan `php -l` untuk verifikasi sebenarnya.

> **Sistem sudah production-ready** dengan responsive design, SQA audit passed, dan semua bug resolved.
