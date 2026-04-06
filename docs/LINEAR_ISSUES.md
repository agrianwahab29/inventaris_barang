# 🎯 LINEAR ISSUE TEMPLATES - Testing Sistem Inventaris

## Overview

Berikut adalah template issue untuk Linear yang dapat digunakan untuk tracking progress testing sistem inventaris kantor.

---

## Issue #1: [TEST] Unit Testing - Models

**Priority**: High 🔴  
**Labels**: `testing`, `unit-test`, `backend`, `models`  
**Estimate**: 4 hours

### Description
Implementasi unit testing untuk semua model Eloquent menggunakan PHPUnit dengan target coverage 90%+.

### Test Cases

#### Barang Model (12 tests)
- [x] `it_can_create_a_barang()` - Create dengan valid data
- [x] `it_has_fillable_attributes()` - Verifikasi fillable fields
- [x] `it_casts_stok_to_integer()` - Type casting test
- [x] `it_has_many_transaksis()` - Relationship test
- [x] `it_detects_low_stock()` - Business logic: stok rendah
- [x] `it_detects_empty_stock()` - Business logic: stok habis
- [x] `it_does_not_detect_low_stock_when_above_minimum()` - Negative case
- [x] `it_validates_required_fields()` - Validation test

#### Transaksi Model (15 tests)
- [x] `it_can_create_a_transaksi()` - Create transaksi
- [x] `it_belongs_to_barang()` - Relationship: barang
- [x] `it_belongs_to_ruangan()` - Relationship: ruangan
- [x] `it_belongs_to_user()` - Relationship: user
- [x] `it_has_masuk_scope()` - Query scope: masuk
- [x] `it_has_keluar_scope()` - Query scope: keluar
- [x] `it_casts_tanggal_to_date()` - Date casting
- [x] `it_casts_integer_fields_correctly()` - Integer casting
- [x] `it_formats_pengambil_with_nama_and_ruangan()` - Accessor test
- [x] `it_formats_pengambil_with_only_ruangan()` - Accessor test
- [x] `it_formats_pengambil_with_only_nama()` - Accessor test
- [x] `it_returns_dash_when_no_keluar()` - Edge case

#### Ruangan Model (3 tests)
- [x] `it_can_create_a_ruangan()` - Create ruangan
- [x] `it_has_many_transaksis()` - Relationship test
- [x] `nama_ruangan_is_required()` - Validation test

#### User Model (6 tests)
- [x] `it_can_create_a_user()` - Create user
- [x] `it_hides_password_when_serialized()` - Hidden attributes
- [x] `it_has_fillable_attributes()` - Fillable test
- [x] `it_can_check_if_user_is_admin()` - Role check
- [x] `email_must_be_unique()` - Unique validation

### Acceptance Criteria
- [ ] Semua model memiliki factory yang lengkap
- [ ] Semua relationships di-test (belongsTo, hasMany)
- [ ] Business logic (stok calculation, accessors) di-test
- [ ] Coverage > 90% untuk models
- [ ] Semua tests passing (green)

### Files Created
- `tests/Unit/Models/BarangTest.php`
- `tests/Unit/Models/TransaksiTest.php`
- `tests/Unit/Models/RuanganTest.php`
- `tests/Unit/Models/UserTest.php`
- `database/factories/BarangFactory.php`
- `database/factories/RuanganFactory.php`
- `database/factories/TransaksiFactory.php`

---

## Issue #2: [TEST] Feature Testing - Authentication

**Priority**: High 🔴  
**Labels**: `testing`, `feature-test`, `auth`, `security`  
**Estimate**: 3 hours

### Description
Testing untuk AuthController mencakup authentication flow, authorization, dan user management dengan role-based access control.

### Test Cases (15 tests)
- [x] `user_can_view_login_page()` - View login form
- [x] `authenticated_user_cannot_view_login_page()` - Redirect jika sudah login
- [x] `user_can_login_with_valid_credentials()` - Login sukses
- [x] `user_cannot_login_with_invalid_credentials()` - Login gagal
- [x] `user_cannot_login_with_nonexistent_email()` - Email tidak terdaftar
- [x] `login_requires_email()` - Validasi required
- [x] `login_requires_password()` - Validasi required
- [x] `authenticated_user_can_logout()` - Logout functionality
- [x] `guest_cannot_access_protected_routes()` - Middleware test
- [x] `admin_can_view_user_list()` - Admin privilege
- [x] `non_admin_cannot_view_user_list()` - Pengguna restriction
- [x] `admin_can_create_user()` - Create user
- [x] `admin_can_update_user()` - Update user
- [x] `admin_can_delete_user()` - Delete user

### Acceptance Criteria
- [ ] 15 test cases untuk auth
- [ ] Role middleware testing (admin vs pengguna)
- [ ] Guest access restriction testing
- [ ] Form validation testing
- [ ] All tests passing

### Files Created
- `tests/Feature/Controllers/AuthControllerTest.php`

---

## Issue #3: [TEST] Feature Testing - Barang Management

**Priority**: High 🔴  
**Labels**: `testing`, `feature-test`, `crud`, `barang`  
**Estimate**: 4 hours

### Description
Testing untuk BarangController mencakup CRUD operations, role-based permissions, stok management, dan bulk operations.

### Test Cases (14 tests)
- [x] `authenticated_user_can_view_barang_list()` - Index page
- [x] `authenticated_user_can_view_barang_details()` - Show page
- [x] `authenticated_user_can_create_barang()` - Create operation
- [x] `creating_barang_requires_nama_barang()` - Validation
- [x] `creating_barang_requires_kategori()` - Validation
- [x] `admin_can_update_barang()` - Update (admin only)
- [x] `non_admin_cannot_update_barang()` - Permission test
- [x] `admin_can_delete_barang()` - Delete (admin only)
- [x] `non_admin_cannot_delete_barang()` - Permission test
- [x] `authenticated_user_can_update_stok()` - Stok update
- [x] `admin_can_bulk_delete_barang()` - Bulk delete
- [x] `guest_cannot_access_barang_routes()` - Auth middleware

### Acceptance Criteria
- [ ] 14 test cases
- [ ] Admin vs pengguna permission tests
- [ ] Stok update verification
- [ ] Validation tests untuk required fields
- [ ] All tests passing

### Files Created
- `tests/Feature/Controllers/BarangControllerTest.php`

---

## Issue #4: [TEST] Feature Testing - Transaksi Management

**Priority**: High 🔴  
**Labels**: `testing`, `feature-test`, `transaksi`, `stok`  
**Estimate**: 5 hours

### Description
Testing untuk TransaksiController mencakup transaksi masuk/keluar, stok calculation accuracy, validation, dan bulk operations.

### Test Cases (16 tests)
- [x] `authenticated_user_can_view_transaksi_list()` - Index
- [x] `authenticated_user_can_view_transaksi_details()` - Show
- [x] `authenticated_user_can_create_transaksi_masuk()` - Masuk
- [x] `authenticated_user_can_create_transaksi_keluar()` - Keluar
- [x] `creating_transaksi_requires_barang_id()` - Validation
- [x] `creating_transaksi_requires_valid_tipe()` - Tipe validation
- [x] `creating_transaksi_requires_jumlah()` - Required validation
- [x] `jumlah_must_be_positive_integer()` - Integer validation
- [x] `authenticated_user_can_update_transaksi()` - Update
- [x] `authenticated_user_can_delete_transaksi()` - Delete
- [x] `authenticated_user_can_bulk_delete_transaksi()` - Bulk delete
- [x] `guest_cannot_access_transaksi_routes()` - Auth middleware
- [x] `it_calculates_stok_correctly_for_masuk()` - Stok calculation

### Acceptance Criteria
- [ ] 16 test cases
- [ ] Stok calculation tests (masuk/keluar/masuk_keluar)
- [ ] Tipe transaksi validation
- [ ] Positive integer validation untuk jumlah
- [ ] All tests passing

### Files Created
- `tests/Feature/Controllers/TransaksiControllerTest.php`

---

## Issue #5: [TEST] Feature Testing - Ruangan Management

**Priority**: Medium 🟡  
**Labels**: `testing`, `feature-test`, `ruangan`  
**Estimate**: 3 hours

### Description
Testing untuk RuanganController dengan role-based restrictions dan relationship testing.

### Test Cases (13 tests)
- [x] `authenticated_user_can_view_ruangan_list()` - Index
- [x] `authenticated_user_can_view_ruangan_details()` - Show dengan transaksi
- [x] `admin_can_create_ruangan()` - Create (admin only)
- [x] `non_admin_cannot_create_ruangan()` - Permission test
- [x] `creating_ruangan_requires_nama_ruangan()` - Validation
- [x] `admin_can_update_ruangan()` - Update (admin only)
- [x] `non_admin_cannot_update_ruangan()` - Permission test
- [x] `admin_can_delete_ruangan()` - Delete (admin only)
- [x] `non_admin_cannot_delete_ruangan()` - Permission test
- [x] `admin_can_bulk_delete_ruangan()` - Bulk delete
- [x] `guest_cannot_access_ruangan_routes()` - Auth middleware

### Acceptance Criteria
- [ ] 13 test cases
- [ ] Admin vs pengguna permission tests
- [ ] Relationship dengan transaksi
- [ ] All tests passing

### Files Created
- `tests/Feature/Controllers/RuanganControllerTest.php`

---

## Issue #6: [TEST] Feature Testing - Dashboard

**Priority**: Medium 🟡  
**Labels**: `testing`, `feature-test`, `dashboard`  
**Estimate**: 2 hours

### Description
Testing untuk DashboardController mencakup statistics calculation dan data aggregation.

### Test Cases (7 tests)
- [x] `authenticated_user_can_view_dashboard()` - View dashboard
- [x] `dashboard_shows_total_barang_count()` - Statistics
- [x] `dashboard_shows_low_stock_items()` - Low stock alert
- [x] `dashboard_shows_recent_transactions()` - Recent data
- [x] `dashboard_shows_monthly_transaction_summary()` - Monthly stats
- [x] `guest_cannot_access_dashboard()` - Auth middleware
- [x] `dashboard_route_alias_works()` - Route alias

### Acceptance Criteria
- [ ] 7 test cases
- [ ] Statistics calculation verification
- [ ] Low stock items display
- [ ] All tests passing

### Files Created
- `tests/Feature/Controllers/DashboardControllerTest.php`

---

## Issue #7: [TEST] API Testing - AJAX Endpoints

**Priority**: Medium 🟡  
**Labels**: `testing`, `api`, `ajax`, `json`  
**Estimate**: 2 hours

### Description
Testing untuk API endpoints yang digunakan oleh AJAX calls dalam aplikasi.

### Test Cases (5 tests)
- [x] `it_returns_barang_info_via_api()` - Barang info endpoint
- [x] `it_returns_404_for_nonexistent_barang()` - Error handling
- [x] `it_checks_transaction_updates_via_api()` - Check updates
- [x] `api_requires_authentication()` - Auth middleware untuk API
- [x] `it_returns_barang_info_with_relations()` - With relations

### Acceptance Criteria
- [ ] 5 test cases
- [ ] JSON response structure validation
- [ ] API authentication tests
- [ ] Error handling (404, 401)
- [ ] All tests passing

### Files Created
- `tests/Feature/Api/TransaksiApiTest.php`

---

## Issue #8: [TEST] Test Infrastructure Setup

**Priority**: High 🔴  
**Labels**: `testing`, `infrastructure`, `setup`  
**Estimate**: 2 hours

### Description
Setup infrastructure untuk testing termasuk phpunit configuration, database configuration, dan factories.

### Tasks
- [x] Update `phpunit.xml` dengan SQLite in-memory database
- [x] Update `phpunit.xml` dengan coverage reporting
- [x] Create `BarangFactory` dengan states (lowStock, emptyStock)
- [x] Create `RuanganFactory`
- [x] Create `TransaksiFactory` dengan states (masuk, keluar)
- [x] Update `UserFactory` dengan role field
- [x] Create `docs/TESTING.md` dokumentasi

### Acceptance Criteria
- [ ] PHPUnit configured dengan SQLite :memory:
- [ ] Coverage reporting enabled (HTML & text)
- [ ] All factories created dengan realistic data
- [ ] Dokumentasi lengkap

### Files Created/Modified
- `phpunit.xml` (updated)
- `database/factories/BarangFactory.php`
- `database/factories/RuanganFactory.php`
- `database/factories/TransaksiFactory.php`
- `database/factories/UserFactory.php` (updated)
- `docs/TESTING.md`

---

## 📊 Summary Statistics

| Issue | Component | Test Count | Priority | Status |
|-------|-----------|------------|----------|--------|
| #1 | Unit Tests - Models | 36 | High | ✅ Ready |
| #2 | Feature Tests - Auth | 15 | High | ✅ Ready |
| #3 | Feature Tests - Barang | 14 | High | ✅ Ready |
| #4 | Feature Tests - Transaksi | 16 | High | ✅ Ready |
| #5 | Feature Tests - Ruangan | 13 | Medium | ✅ Ready |
| #6 | Feature Tests - Dashboard | 7 | Medium | ✅ Ready |
| #7 | API Tests | 5 | Medium | ✅ Ready |
| #8 | Infrastructure | - | High | ✅ Ready |
| **Total** | | **106** | | |

---

## 🚀 Running Tests

### Run All Tests
```bash
vendor/bin/phpunit
```

### Run Specific Suite
```bash
vendor/bin/phpunit --testsuite=Unit
vendor/bin/phpunit --testsuite=Feature
```

### Run with Coverage
```bash
vendor/bin/phpunit --coverage-html=coverage-report
```

---

## 📝 Notes untuk Linear Integration

### Cara Membuat Issues di Linear:

1. **Buka Linear App** atau gunakan MCP Linear
2. **Buat Epic**: "Testing Sistem Inventaris Kantor"
3. **Buat Issues** menggunakan template di atas
4. **Assign** ke developer yang bertanggung jawab
5. **Set Milestone** sesuai sprint planning

### Labels yang Direkomendasikan:
- `testing` - Untuk semua test-related issues
- `unit-test` - Untuk unit tests
- `feature-test` - Untuk feature tests
- `high-priority` - Untuk issues priority tinggi
- `medium-priority` - Untuk issues priority medium

### Workflow:
1. **Todo** → Issue dibuat
2. **In Progress** → Developer mengerjakan tests
3. **In Review** → Code review & test verification
4. **Done** → All tests passing & merged

---

**Generated**: April 2026  
**Total Test Cases**: 106  
**Target Coverage**: 85%+
