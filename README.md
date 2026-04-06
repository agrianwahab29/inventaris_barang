# 🏢 Sistem Inventaris Kantor

[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![Tests](https://img.shields.io/badge/tests-106%20passing-brightgreen)](docs/TESTING.md)

> Sistem manajemen inventaris barang kantor dengan tracking transaksi dan stok opname yang lengkap.

## 📖 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Penggunaan](#penggunaan)
- [Testing](#testing)
- [Keamanan](#keamanan)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## 🎯 Tentang Proyek

Sistem Inventaris Kantor adalah aplikasi web berbasis Laravel untuk mengelola inventaris barang di lingkungan kantor. Sistem ini memungkinkan tracking real-time stok barang, transaksi masuk/keluar, dan pembuatan laporan stok opname berkala.

### 🎓 Tujuan Pengembangan

- Mengotomatiskan pencatatan inventaris barang
- Tracking transaksi barang masuk dan keluar
- Monitoring stok rendah secara otomatis
- Menghasilkan laporan stok opname berkala
- Manajemen multi-user dengan role-based access control

## ✨ Fitur Utama

### 📦 Manajemen Barang
- ✅ CRUD barang dengan validasi lengkap
- ✅ Kode barang otomatis
- ✅ Pencatatan satuan dan stok
- ✅ Export data ke Excel
- ✅ Bulk delete barang
- ✅ Update stok manual
- ✅ Alert stok rendah (< 5 unit)
- ✅ Relasi dengan ruangan penyimpanan

### 📋 Manajemen Transaksi
- ✅ Transaksi barang masuk
- ✅ Transaksi barang keluar
- ✅ Transaksi masuk & keluar sekaligus
- ✅ Pencatatan pengambil (nama/keterangan)
- ✅ Otomatis update stok barang
- ✅ Export transaksi ke Excel
- ✅ Filter berdasarkan tanggal, tipe, barang
- ✅ Bulk delete transaksi
- ✅ AJAX get info barang real-time
- ✅ Check updates untuk real-time monitoring

### 🏠 Manajemen Ruangan
- ✅ CRUD ruangan penyimpanan
- ✅ Relasi dengan transaksi
- ✅ Bulk delete ruangan
- ✅ Filter transaksi per ruangan
- ✅ Statistik per ruangan

### 📊 Dashboard & Statistik
- ✅ Total barang dan total stok
- ✅ Total transaksi (masuk/keluar)
- ✅ Alert stok rendah (< 5 unit)
- ✅ Transaksi terbaru (5 terakhir)
- ✅ Summary bulanan
- ✅ Grafik interaktif

### 📄 Laporan & Dokumen
- ✅ Export transaksi ke Excel (.xlsx)
- ✅ Export barang ke Excel (.xlsx)
- ✅ Laporan stok opname triwulan (DOCX)
- ✅ Surat tanda terima barang (DOCX)
- ✅ Format laporan profesional

### 👥 Manajemen User
- ✅ Role-based access control (Admin/Pengguna)
- ✅ CRUD user (Admin only)
- ✅ Bulk delete user
- ✅ Edit profil user
- ✅ Password hashing otomatis

### 🔐 Keamanan
- ✅ Authentication & Authorization
- ✅ Rate limiting login (5 attempts/minute)
- ✅ Rate limiting user creation (10/minute)
- ✅ Middleware role protection
- ✅ CSRF protection
- ✅ XSS protection
- ✅ SQL injection protection (Eloquent ORM)

### 🧪 Testing
- ✅ 106 Test Cases (Unit + Feature + API)
- ✅ TDD (Test-Driven Development)
- ✅ Coverage > 85%
- ✅ PHPUnit 9.x
- ✅ Model Factories

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 8.x
- **Language**: PHP 8.0+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **ORM**: Eloquent ORM

### Frontend
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS + Axios
- **Icons**: Bootstrap Icons

### Libraries & Packages
- **maatwebsite/excel**: Export data ke Excel
- **phpoffice/phpword**: Generate dokumen Word
- **guzzlehttp/guzzle**: HTTP client

### Development Tools
- **PHPUnit 9.x**: Unit & Feature Testing
- **Faker**: Generate dummy data
- **Laravel Tinker**: REPL console

## 📥 Instalasi

### Prerequisites

- PHP >= 8.0
- Composer
- MySQL >= 5.7
- Node.js & NPM (untuk frontend assets)

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/agrianwahab29/inventaris_barang.git
   cd inventaris_barang
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   # Copy .env.example ke .env
   cp .env.example .env

   # Generate application key
   php artisan key:generate
   ```

4. **Konfigurasi database**
   
   Edit file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventaris_kantor
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Migrasi database**
   ```bash
   php artisan migrate
   ```

6. **Seed data awal (opsional)**
   ```bash
   php artisan db:seed
   ```

7. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```
   
   Buka browser: `http://localhost:8000`

### Login Default

Setelah seeding, gunakan akun default:

- **Admin**:
  - Email: `admin@example.com`
  - Password: `password`

- **Pengguna**:
  - Email: `user@example.com`
  - Password: `password`

⚠️ **Penting**: Ganti password default setelah login pertama!

## ⚙️ Konfigurasi

### Environment Variables

File `.env` berisi konfigurasi penting:

```env
APP_NAME="Inventaris Kantor"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris_kantor
DB_USERNAME=root
DB_PASSWORD=

# Rate Limiting (opsional)
RATE_LIMIT_LOGIN=5,1
RATE_LIMIT_USER_CREATION=10,1
```

### Konfigurasi Tambahan

**Upload File (jika diperlukan):**
```env
FILESYSTEM_DRIVER=local
```

**Email (opsional):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@example.com
```

## 🚀 Penggunaan

### Sebagai Admin

1. **Login** dengan akun admin
2. **Kelola Barang**: Tambah, edit, hapus barang
3. **Kelola Ruangan**: Buat ruangan penyimpanan
4. **Kelola Transaksi**: Catat barang masuk/keluar
5. **Kelola User**: Tambah/edit user baru
6. **Lihat Dashboard**: Monitoring stok dan transaksi
7. **Export Laporan**: Download Excel/Word

### Sebagai Pengguna

1. **Login** dengan akun pengguna
2. **Lihat Barang**: Browse daftar barang
3. **Catat Transaksi**: Buat transaksi masuk/keluar
4. **Lihat Dashboard**: Monitoring stok
5. **Export Laporan**: Download Excel (transaksi sendiri)

### Command Line

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Run seeders
php artisan db:seed

# Export transaksi
php artisan export:transaksi

# Import CSV (jika ada)
php artisan import:csv-transaksi
```

## 🧪 Testing

Proyek ini menggunakan Test-Driven Development (TDD) dengan PHPUnit.

### Menjalankan Semua Test

```bash
php artisan test
```

### Test Spesifik

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# Specific file
php artisan test tests/Unit/Models/BarangTest.php

# Specific method
php artisan test --filter=test_user_can_create_barang
```

### Coverage Report

```bash
# Text coverage
php artisan test --coverage

# HTML coverage
php artisan test --coverage --html=coverage-report
```

### Test Statistics

- **Total Test Cases**: 106 tests
- **Unit Tests**: 30+ tests
- **Feature Tests**: 70+ tests
- **API Tests**: 5+ tests
- **Coverage Target**: > 85%

📖 Dokumentasi lengkap testing: [docs/TESTING.md](docs/TESTING.md)

## 🔒 Keamanan

### Best Practices yang Diterapkan

1. **Authentication & Authorization**
   - Login dengan rate limiting (5 attempts/minute)
   - Role-based access control (Admin/Pengguna)
   - Session management yang aman

2. **Input Validation**
   - Validasi semua input dari user
   - CSRF protection di semua form
   - XSS protection otomatis (Blade templates)

3. **Database Security**
   - Eloquent ORM mencegah SQL injection
   - Prepared statements otomatis
   - Mass assignment protection ($fillable)

4. **File Security**
   - File upload validation (jika ada)
   - Secure storage path
   - No sensitive files di public folder

5. **Debug Endpoints Removed**
   - `/check-seed` - DIHAPUS (was exposing storage paths)
   - `/seed-transaksi` - DIHAPUS (was allowing DB manipulation)

### Security Recommendations

- ✅ Ganti password default setelah instalasi
- ✅ Set `APP_DEBUG=false` di production
- ✅ Set `APP_ENV=production` di production
- ✅ Gunakan HTTPS di production
- ✅ Backup database secara berkala
- ✅ Update dependencies secara rutin
- ✅ Jangan commit file `.env`

### Vulnerability Reporting

Jika menemukan celah keamanan, harap laporkan secara private melalui:
- Email: [security@example.com]
- Jangan buat public issue untuk vulnerability

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan ikuti langkah berikut:

### Cara Berkontribusi

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/fitur-baru`)
3. Commit perubahan (`git commit -m 'feat: tambah fitur baru'`)
4. Push ke branch (`git push origin feature/fitur-baru`)
5. Buat Pull Request

### Commit Message Format

Gunakan format berikut:

```
type(scope): deskripsi singkat

type: feat|fix|docs|style|refactor|test|chore
scope: barang|transaksi|ruangan|auth|dashboard
```

Contoh:
- `feat(transaksi): add export to Excel functionality`
- `fix(barang): correct stock calculation bug`
- `docs: update README with installation steps`

### Code Style

- Ikuti PSR-12 PHP Coding Style
- Gunakan meaningful variable names
- Tambahkan komentar untuk complex logic
- Tulis test untuk fitur baru

### Testing Requirements

- Pastikan semua test passing sebelum PR
- Tambah test untuk fitur baru
- Coverage minimal 80% untuk kode baru

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

```
Copyright (c) 2026 Inventaris Kantor

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

## 📞 Support & Kontak

- **Repository**: [https://github.com/agrianwahab29/inventaris_barang](https://github.com/agrianwahab29/inventaris_barang)
- **Issues**: [GitHub Issues](https://github.com/agrianwahab29/inventaris_barang/issues)
- **Email**: [support@example.com]

## 🙏 Acknowledgments

- [Laravel Framework](https://laravel.com)
- [Bootstrap](https://getbootstrap.com)
- [Maatwebsite Excel](https://github.com/Maatwebsite/Laravel-Excel)
- [PHPOffice PHPWord](https://github.com/PHPOffice/PHPWord)

---

**Dibuat dengan ❤️ menggunakan Laravel 8.x**

**Last Updated**: April 2026
