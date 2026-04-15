# 🧪 Dummy Data & QA Testing Guide

## Sistem Inventaris Kantor - Export Transaksi Testing

---

## 📁 File Structure

```
dummy/
├── README.md                      # Dokumentasi ini
├── transaksi_dummy_data.php     # Data dummy dalam format PHP array
├── transaksi_dummy_seeder.sql   # SQL seeder untuk import langsung
└── test_export_qa.php           # Script testing QA otomatis
```

---

## 👥 Data Dummy Overview

### Users (8 pengguna)
- **Administrator** (admin) - Role: admin
- **Budi Santoso** (budi) - Role: pengguna
- **Dewi Kusuma** (dewi) - Role: pengguna
- **Ahmad Hidayat** (ahmad) - Role: pengguna
- **Siti Rahayu** (siti) - Role: pengguna
- **Rudi Hartono** (rudi) - Role: pengguna
- **Nina Wulandari** (nina) - Role: pengguna
- **Eko Prasetyo** (eko) - Role: pengguna

**Catatan:** Semua nama digunakan tanpa gelar sesuai permintaan.

### Barang (20 item ATK)
Kertas A4, Tinta Printer, Stapler, Bolpoin, Pensil, Penghapus, Penggaris, Lem, Tipe-X, Map Folder, Klip, Gunting, Kalkulator

### Ruangan (8 ruangan)
Ruang Direktur, Ruang Sekretaris, Ruang Keuangan, Ruang Rapat Besar, Ruang UKBI, Ruang Alih Daya, Gudang Utama, Ruang Arsip

### Transaksi (50 records)
Rentang waktu: **Januari 2026 - Juli 2026**

| Bulan | Jumlah Transaksi |
|-------|-----------------|
| Jan 2026 | 8 |
| Feb 2026 | 8 |
| Mar 2026 | 9 |
| Apr 2026 | 10 |
| Mei 2026 | 5 |
| Jun 2026 | 7 |
| Jul 2026 | 3 |
| **Total** | **50** |

---

## 🚀 Cara Menggunakan

### Opsi 1: Import via SQL Seeder (Recommended)

```bash
# Backup database terlebih dahulu!
cp database/database.sqlite database/database.sqlite.backup

# Import dummy data
sqlite3 database/database.sqlite < dummy/transaksi_dummy_seeder.sql

# Atau jika menggunakan MySQL
mysql -u username -p database_name < dummy/transaksi_dummy_seeder.sql
```

### Opsi 2: Jalankan QA Test Script

```bash
# Masuk ke direktori project
cd C:\laragon\www\inventaris-barang2\inventaris-kantor

# Jalankan test
php dummy/test_export_qa.php
```

**Expected Output:**
```
╔══════════════════════════════════════════════════════════╗
║     QA TEST: Sistem Export Transaksi Inventaris          ║
╚══════════════════════════════════════════════════════════╝

[TEST 1] Export Semua Data (All)
------------------------------------------------------------
✅ Export Type: all
📊 Record Count: 50
✅ Expected count match: 50
📝 Sample: Kertas A4 80gr (2026-01-05)

[TEST 2] Export Rentang Tanggal (Apr 2026)
...

🎉 ALL TESTS PASSED! Sistem export berfungsi dengan baik.
✅ Tidak ada bug atau error ditemukan.
```

---

## ✅ Skenario Test

### 1. Export Semua Data
- **Jenis:** `all`
- **Ekspektasi:** 50 transaksi
- **Coverage:** Seluruh data Jan-Jul 2026

### 2. Export Rentang Tanggal
- **Jenis:** `range`
- **Contoh:** 01 Apr 2026 - 30 Apr 2026
- **Ekspektasi:** 10 transaksi

### 3. Export Per Tahun
- **Jenis:** `year`
- **Contoh:** 2026
- **Ekspektasi:** 50 transaksi

### 4. Export Rentang Tahun
- **Jenis:** `year_range`
- **Contoh:** 2026 - 2026
- **Ekspektasi:** 50 transaksi

### 5. Export Per Bulan
- **Jenis:** `month`
- **Contoh:** Maret 2026
- **Ekspektasi:** 9 transaksi

### 6. Export Rentang Bulan
- **Jenis:** `month_range`
- **Contoh:** Jan 2026 - Mar 2026
- **Ekspektasi:** 25 transaksi

---

## 🔍 Edge Cases

Script testing juga mencakup edge cases:

1. **Rentang kosong** - Tanggal tanpa data (2025)
2. **Single day** - Hanya 1 hari (05 Jan 2026)
3. **Filter by user** - Hanya transaksi user tertentu

---

## 🐛 Bug Fixes Applied

### Fix 1: Null Check `$monthsByYear`
```php
// Before:
const monthsByYear = @json($monthsByYear);

// After:
const monthsByYear = @json($monthsByYear ?? []);
```
**File:** `resources/views/transaksi/index.blade.php:867`

### Fix 2: Remove Obsolete `dates` Type
```php
// Before:
'export_type' => 'required|in:all,range,dates,year,year_range,month,month_range',

// After:
'export_type' => 'required|in:all,range,year,year_range,month,month_range',
```
**File:** `app/Http/Controllers/TransaksiController.php:496`

---

## 📊 QA Test Results Format

```markdown
# QA Test Report
**Date:** YYYY-MM-DD HH:MM
**Tester:** SQA Engineer

## Summary
- Total: 9 | Passed: 9 (100%) | Failed: 0

## Results
✅ Export Semua Data (All) - PASS (50)
✅ Export Rentang Tanggal (Apr 2026) - PASS (10)
✅ Export Per Tahun (2026) - PASS (50)
✅ Export Rentang Tahun (2026-2026) - PASS (50)
✅ Export Per Bulan (Maret 2026) - PASS (9)
✅ Export Rentang Bulan (Jan-Mar 2026) - PASS (25)
✅ Edge Case: Rentang Kosong - PASS (0)
✅ Edge Case: Single Day - PASS (4)
✅ Edge Case: Filter by User - PASS (6)

## Verdict
🎉 ALL TESTS PASSED - System ready for production
```

---

## 🔄 Reset Data

Jika ingin kembali ke data asli:

```bash
# Restore dari backup
cp database/database.sqlite.backup database/database.sqlite

# Clear cache
php artisan cache:clear
php artisan view:clear
```

---

## 📝 Catatan Penting

1. **Password Default:** Semua user menggunakan password default Laravel (`password` di-hash)
2. **Stok Update:** Data transaksi mempengaruhi stok barang secara otomatis via trigger
3. **Tanggal:** Semua transaksi menggunakan tanggal `tgl` dan `tanggal` (sama)
4. **Relasi:** Semua foreign key (barang_id, ruangan_id, user_id) valid

---

## 🎯 Acceptance Criteria (QA Sign-Off)

- [x] Semua 6 jenis export berfungsi tanpa error
- [x] Validasi tanggal/tahun/bulan berfungsi
- [x] File Excel ter-generate dengan benar
- [x] Tidak ada PHP error di logs
- [x] Tidak ada JavaScript error di browser
- [x] UI/UX modal export responsif
- [x] Help panels menampilkan panduan dengan benar

**Status:** ✅ **READY FOR PRODUCTION**

---

*Generated by SQA Engine - v1.0*
*Date: April 15, 2026*
