# Task Context: User Status (Aktif/Nonaktif)

Session ID: 2025-01-09-user-status
Created: 2025-01-09T10:00:00+07:00
Status: COMPLETED

## Current Request
Menambahkan fitur status user (aktif/nonaktif) agar user dummy bisa disembunyikan dari daftar tanpa menghapus data transaksi mereka.

## Context Files (Standards to Follow)
- `C:/Users/agrian wahab/.config/opencode/skills/laravel-patterns/SKILL.md` - Eloquent patterns, scopes, $fillable, $casts
- `C:/Users/agrian wahab/.config/opencode/skills/database-migrations/SKILL.md` - Safe column addition with defaults
- `C:/Users/agrian wahab/.config/opencode/skills/laravel-security/SKILL.md` - Mass assignment protection

## Reference Files (Source Material to Look At)
- `app/Models/User.php` - Model User yang akan ditambahkan status
- `app/Http/Controllers/UserController.php` - Controller untuk update filter
- `database/migrations/2014_10_12_000000_create_users_table.php` - Migration existing
- `resources/views/users/index.blade.php` - View daftar user
- `resources/views/users/create.blade.php` - View form create
- `resources/views/users/edit.blade.php` - View form edit
- `database/seeders/ComprehensiveDummyDataSeeder.php` - Seeder user dummy

## External Docs Fetched
N/A - Using Laravel built-in patterns

## Components
1. **Migration**: Tambah kolom `status` (enum: 'aktif', 'nonaktif') dengan default 'aktif'
2. **Model**: Update User model dengan $fillable, $casts, dan query scopes
3. **Controller**: Update UserController - filter index hanya user aktif, tapi form transaksi tetap tampilkan semua
4. **Views**: 
   - Update form create/edit dengan toggle status
   - Update index dengan indikator status
   - Update dropdown transaksi untuk tetap tampilkan user nonaktif (untuk data historis)
5. **Seeder**: Update user dummy dengan status 'nonaktif'

## Constraints
- User dummy (Budi, Ani, Dedi, Siti, Rudi) sudah punya transaksi, tidak boleh dihapus
- Status 'nonaktif' hanya menyembunyikan dari daftar, tidak menghapus data
- Form transaksi harus tetap bisa pilih user nonaktif untuk melihat data historis
- Gunakan enum untuk status (aktif/nonaktif) bukan boolean untuk readability

## Exit Criteria
- [x] Migration berhasil menambah kolom status
- [x] Model User memiliki scopes aktif() dan nonaktif()
- [x] Daftar user (index) hanya menampilkan user aktif by default
- [x] Form create/edit user memiliki toggle status
- [x] User dummy di-set status 'nonaktif'
- [x] Form transaksi tetap bisa pilih user nonaktif
- [x] Filter toggle untuk melihat user nonaktif di index

## Status: COMPLETED
