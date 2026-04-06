# 🧪 Testing Documentation - Sistem Inventaris Kantor

## Overview

Dokumentasi ini menjelaskan struktur testing untuk Sistem Inventaris Kantor menggunakan Laravel TDD (Test-Driven Development) dengan PHPUnit.

## 📊 Testing Structure

```
tests/
├── Unit/
│   └── Models/
│       ├── BarangTest.php          # Unit test untuk model Barang
│       ├── TransaksiTest.php       # Unit test untuk model Transaksi
│       ├── RuanganTest.php         # Unit test untuk model Ruangan
│       └── UserTest.php            # Unit test untuk model User
├── Feature/
│   ├── Controllers/
│   │   ├── AuthControllerTest.php      # Test authentication
│   │   ├── BarangControllerTest.php     # Test Barang CRUD
│   │   ├── TransaksiControllerTest.php  # Test Transaksi CRUD
│   │   ├── RuanganControllerTest.php    # Test Ruangan CRUD
│   │   └── DashboardControllerTest.php  # Test Dashboard
│   └── Api/
│       └── TransaksiApiTest.php    # Test API endpoints
├── CreatesApplication.php
└── TestCase.php
```

## 🎯 Test Coverage Areas

### 1. Unit Tests (Models)
- **BarangTest**: Testing model attributes, relationships, stok logic
- **TransaksiTest**: Testing tipe transaksi, scopes, accessors
- **RuanganTest**: Testing model relationships
- **UserTest**: Testing authentication attributes

### 2. Feature Tests (Controllers)
- **AuthControllerTest**: Login, logout, user management (15 test cases)
- **BarangControllerTest**: CRUD operations, role-based access (14 test cases)
- **TransaksiControllerTest**: Transaksi masuk/keluar, stok calculation (16 test cases)
- **RuanganControllerTest**: CRUD with admin restrictions (13 test cases)
- **DashboardControllerTest**: Dashboard statistics, views (7 test cases)

### 3. API Tests
- **TransaksiApiTest**: AJAX endpoints, JSON responses (5 test cases)

## 🚀 Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

### Run Specific Test File
```bash
php artisan test tests/Unit/Models/BarangTest.php
php artisan test tests/Feature/Controllers/AuthControllerTest.php
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Run with HTML Coverage Report
```bash
php artisan test --coverage --html=coverage-report
```

## 📈 Test Metrics

### Total Test Cases: **70+ tests**

| Component | Test Count | Coverage Target |
|-----------|------------|-----------------|
| Unit Tests | 30+ | 90% |
| Feature Tests | 35+ | 85% |
| API Tests | 5+ | 80% |
| **Total** | **70+** | **85%+** |

## 🔐 Testing Scenarios

### Authentication Testing
- ✅ Login dengan kredensial valid
- ✅ Login dengan kredensial invalid
- ✅ Logout functionality
- ✅ Role-based access control (admin vs pengguna)
- ✅ Guest access restrictions

### Barang Management Testing
- ✅ Create barang dengan valid data
- ✅ Validation errors untuk required fields
- ✅ Update barang (admin only)
- ✅ Delete barang (admin only)
- ✅ Bulk delete functionality
- ✅ Update stok
- ✅ Stok rendah detection

### Transaksi Testing
- ✅ Create transaksi masuk
- ✅ Create transaksi keluar
- ✅ Stok calculation accuracy
- ✅ Validation untuk tipe transaksi
- ✅ Update dan delete transaksi
- ✅ Bulk delete transaksi
- ✅ Pengambil formatting

### Ruangan Testing
- ✅ CRUD operations dengan role restrictions
- ✅ Relationship dengan transaksi
- ✅ Bulk operations

### Dashboard Testing
- ✅ Statistics calculation
- ✅ Low stock items display
- ✅ Recent transactions
- ✅ Monthly summary

## 🔄 TDD Workflow

### Red-Green-Refactor Cycle

1. **Red**: Write failing test
   ```bash
   php artisan test --filter=test_name
   # Test should fail
   ```

2. **Green**: Implement minimal code
   ```bash
   # Edit implementation
   php artisan test --filter=test_name
   # Test should pass
   ```

3. **Refactor**: Improve code quality
   ```bash
   php artisan test
   # All tests should still pass
   ```

## 📝 Linear Integration

### Issue Templates untuk Linear

#### Issue 1: Unit Testing - Models
```
Title: [TEST] Unit Testing untuk Models
Priority: High
Labels: testing, unit-test, backend

Description:
Implementasi unit testing untuk semua model:
- BarangTest: 12 test cases
- TransaksiTest: 15 test cases  
- RuanganTest: 3 test cases
- UserTest: 6 test cases

Acceptance Criteria:
- [ ] Semua model memiliki factory
- [ ] Semua relationships di-test
- [ ] Business logic (stok calculation) di-test
- [ ] Coverage > 90%
```

#### Issue 2: Feature Testing - Authentication
```
Title: [TEST] Feature Testing - Authentication & Authorization
Priority: High
Labels: testing, feature-test, auth

Description:
Testing untuk AuthController mencakup:
- Login/logout functionality
- Role-based access control
- User management (admin only)
- Validation tests

Acceptance Criteria:
- [ ] 15 test cases untuk auth
- [ ] Role middleware testing
- [ ] Guest access restriction testing
- [ ] All tests passing
```

#### Issue 3: Feature Testing - Barang Controller
```
Title: [TEST] Feature Testing - Barang Management
Priority: High
Labels: testing, feature-test, crud

Description:
Testing untuk BarangController mencakup:
- CRUD operations
- Role-based permissions
- Stok update functionality
- Bulk operations
- Validation tests

Acceptance Criteria:
- [ ] 14 test cases
- [ ] Admin vs pengguna permission tests
- [ ] Stok calculation verification
- [ ] All tests passing
```

#### Issue 4: Feature Testing - Transaksi Controller
```
Title: [TEST] Feature Testing - Transaksi Management
Priority: High
Labels: testing, feature-test, transaksi

Description:
Testing untuk TransaksiController mencakup:
- Transaksi masuk/keluar/masuk_keluar
- Stok calculation accuracy
- Validation untuk tipe transaksi
- Bulk operations
- API endpoints

Acceptance Criteria:
- [ ] 16 test cases
- [ ] Stok calculation tests
- [ ] Tipe transaksi validation
- [ ] All tests passing
```

#### Issue 5: API Testing
```
Title: [TEST] API Testing untuk AJAX Endpoints
Priority: Medium
Labels: testing, api, ajax

Description:
Testing untuk API endpoints:
- /api/barang/{id}/info
- /api/transactions/check-updates
- JSON response validation
- Authentication untuk API

Acceptance Criteria:
- [ ] 5 test cases
- [ ] JSON structure validation
- [ ] API authentication tests
- [ ] All tests passing
```

## 🎨 Best Practices

### 1. Test Naming
```php
// Good
public function user_can_create_barang_with_valid_data()

// Bad
public function testCreateBarang()
```

### 2. Test Structure (Arrange-Act-Assert)
```php
public function user_can_create_barang()
{
    // Arrange
    $user = User::factory()->create();
    $data = ['nama_barang' => 'Test'];
    
    // Act
    $response = $this->actingAs($user)->post('/barang', $data);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('barangs', $data);
}
```

### 3. Use Factories
```php
// Good
$user = User::factory()->create();
$barang = Barang::factory()->create();

// Bad
$user = User::create([...]); // Manual creation
```

### 4. RefreshDatabase Trait
```php
class Test extends TestCase
{
    use RefreshDatabase; // Always use for DB tests
    
    // tests...
}
```

## 🔧 Troubleshooting

### Common Issues

1. **"Class not found" errors**
   ```bash
   composer dump-autoload
   ```

2. **Database locked (SQLite)**
   ```bash
   # Use in-memory database for testing
   # Already configured in phpunit.xml
   ```

3. **Factory not found**
   ```bash
   composer dump-autoload
   php artisan cache:clear
   ```

## 📊 Coverage Report

Generate coverage report:
```bash
php artisan test --coverage --html=coverage-report
```

View report:
- Open `coverage-report/index.html` in browser

## 🚀 CI/CD Integration

### GitHub Actions Example
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test --coverage --min=80
```

## 📝 Summary

- **Total Test Files**: 10
- **Total Test Cases**: 70+
- **Coverage Target**: 85%+
- **Test Framework**: PHPUnit 9.x
- **Database**: SQLite in-memory
- **Factories**: 4 (User, Barang, Ruangan, Transaksi)

---

**Last Updated**: April 2026
**Maintainer**: Development Team
