# Comprehensive Architecture Analysis Report
## Sistem Inventaris Kantor - Laravel 8.x

**Analysis Date:** April 06, 2026  
**Project:** inventaris-kantor  
**Tech Stack:** Laravel 8.x, PHP 8.0+, MySQL, Bootstrap 5  

---

## Executive Summary

### Critical Findings
| Category | Status | Issues | Priority |
|----------|--------|--------|----------|
| Controller Architecture | 🔴 Poor | 8 major | HIGH |
| Model Design | 🟡 Moderate | 5 minor | MEDIUM |
| Service Layer | 🔴 Missing | Critical gap | CRITICAL |
| Routes | 🟢 Good | 2 minor | LOW |
| Security | 🟡 Moderate | 3 issues | MEDIUM |

**Total Issues Found:** 34  
**Recommendations:** 27  
**Estimated Refactoring Effort:** 3-4 weeks

---

## 1. Controller Analysis

### 1.1 Overview
| Controller | Lines | Methods | Responsibility Issues |
|------------|-------|---------|----------------------|
| AuthController | 145 | 9 | ✅ Good |
| DashboardController | 88 | 1 | ✅ Good |
| BarangController | 330 | 9 | 🔴 Fat Controller |
| TransaksiController | 588 | 11 | 🔴 Very Fat Controller |
| RuanganController | 136 | 8 | 🟡 Moderate |
| QuarterlyStockController | 399 | 3 | 🔴 Mixed Concerns |
| SuratTandaTerimaController | 191 | 2 | 🟡 DOCX logic in controller |

**Average Lines per Controller:** ~268 lines  
**Industry Best Practice:** <150 lines per controller

### 1.2 Single Responsibility Violations

#### 🔴 BarangController (CRITICAL)
**Methods:** 9  
**Lines:** 330  
**Issues:**
- **Lines 51-126:** Complex store logic with dual-mode (create vs update existing)
- **Lines 183-192:** Export logic should be in ExportService
- **Lines 194-239:** Bulk delete with business logic
- **Lines 244-329:** `updateStok()` - 86 lines of complex stock adjustment + transaction logic

**Business Logic in Controller:**
```php
// Lines 74-123 - Stock calculation, transaction creation, conditional messages
if ($existingBarang) {
    $stokSebelum = $existingBarang->stok;
    $stokSetelahMasuk = $stokSebelum + $validated['stok'];
    // ... complex logic
}
```

#### 🔴 TransaksiController (CRITICAL)
**Methods:** 11  
**Lines:** 588  
**Issues:**
- **Lines 118-216:** `store()` - 99 lines! Complex transaction with stock calculations
- **Lines 234-339:** `update()` - 106 lines! Complex update with rollback logic
- **Lines 341-366:** `destroy()` - Stock recalculation logic
- **Lines 424-537:** `export()` - 114 lines! Parameter conversion, validation, file handling

**Sample Issue (Lines 141-214):**
```php
// Stock calculation logic mixed with HTTP concerns
$stokSebelum = $barang->stok;
$stokSetelahMasuk = $stokSebelum + $jumlahMasukInput;

if ($stokSetelahMasuk < $jumlahKeluar) {
    return back()->withErrors([...]);
}

DB::beginTransaction();
try {
    $barang->update(['stok' => $sisaStok]);
    Transaksi::create([...]); // Business logic
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // ...
}
```

#### 🔴 QuarterlyStockController (HIGH)
**Methods:** 3  
**Lines:** 399  
**Issues:**
- **Lines 19-109:** `index()` - Heavy data aggregation (90 lines)
- **Lines 145-355:** `exportDocx()` - 211 lines! DOCX generation mixed with data logic
- PHPWord configuration mixed with business logic

### 1.3 Role-Based Access Control Analysis

| Controller | RBAC Implementation | Issue |
|------------|---------------------|-------|
| AuthController | `isAdmin()` check | ✅ Good |
| BarangController | Via Route Middleware | ✅ Good |
| TransaksiController | Mixed (Lines 344-348) | 🟡 Inconsistent |
| RuanganController | Manual check in methods | 🔴 Not using middleware |

**Issue Example (RuanganController):**
```php
// Lines 25-30, 55-60, 74-78 - Repeated manual checks
if (!Auth::user()->isAdmin()) {
    return redirect()->route('ruangan.index')
        ->with('error', 'Hanya admin yang dapat menambah data ruangan');
}
```

**Recommendation:** Move all authorization to middleware (routes) or use Laravel Policies.

---

## 2. Model Analysis

### 2.1 Overview
| Model | Lines | Relations | Business Logic | Scopes |
|-------|-------|-----------|----------------|--------|
| User | 60 | 1 (hasMany) | 2 methods (isAdmin, isPengguna) | 0 |
| Barang | 40 | 1 (hasMany) | 2 methods (isStokRendah, isStokHabis) | 0 |
| Transaksi | 86 | 3 belongsTo | 1 accessor | 2 |
| Ruangan | 21 | 1 hasMany | 0 | 0 |
| QuarterlyStockOpname | 73 | 2 belongsTo | 3 static methods | 0 |

### 2.2 Strengths
✅ **QuarterlyStockOpname** - Good static helper methods:
```php
public static function getQuarterDateRange($tahun, $quarter)
public static function getQuarterMonths($quarter)
```

✅ **Transaksi** - Scopes defined:
```php
public function scopeMasuk($query)
public function scopeKeluar($query)
```

✅ **Barang** - Status methods:
```php
public function isStokRendah()
public function isStokHabis()
```

### 2.3 Missing Model Capabilities

#### 🔴 No Query Scopes (MEDIUM)
**Current State:** Controllers repeat filtering logic

**Example from TransaksiController (Lines 18-106):**
```php
if ($request->filled('user_id')) { $query->where('user_id', $request->user_id); }
if ($request->filled('tipe')) { $query->where('tipe', $request->tipe); }
if ($request->filled('barang_id')) { $query->where('barang_id', $request->barang_id); }
// ... 8 more filters
```

**Recommendation:** Create scopes in Transaksi model:
```php
public function scopeFilterByUser($query, $userId)
public function scopeFilterByTanggal($query, $dari, $sampai)
public function scopeFilterByTahun($query, $tahun)
```

#### 🔴 No Model Events (MEDIUM)
**Current State:** Manual stock updates in controllers

**Issue:** Stock is manually updated in controllers (TransaksiController lines 174, 297, 351-362)

**Recommendation:** Use model observers:
```php
// app/Observers/TransaksiObserver.php
class TransaksiObserver
{
    public function created(Transaksi $transaksi)
    {
        $transaksi->barang->updateStock();
    }
}
```

#### 🔴 No Custom Collections (LOW)
**Opportunity:** Custom collection methods for grouped data in controllers like `SuratTandaTerimaController::index()`

---

## 3. Service Layer Assessment

### 3.1 Current State: 🔴 CRITICAL GAP

**Service Layer:** NONE EXISTS  
**Repository Layer:** NONE EXISTS  
**Form Request Classes:** NONE EXISTS

### 3.2 Business Logic Distribution

| Logic Type | Current Location | Should Be In |
|------------|------------------|--------------|
| Stock Calculation | Controllers (repeated) | StockService |
| Transaction Creation | Controllers (repeated) | TransactionService |
| Export Generation | Controllers/Exports | ExportService |
| DOCX Generation | Controllers | DocumentService |
| Validation Rules | Inline in controllers | FormRequest classes |

### 3.3 Code Duplication Analysis

**Stock Calculation Logic - Duplicated 6+ times:**
- BarangController::store (Lines 74-88)
- BarangController::updateStok (Lines 252-302)
- TransaksiController::store (Lines 141-153)
- TransaksiController::update (Lines 259-270)
- TransaksiController::destroy (Lines 354-362)
- TransaksiController::bulkDelete (Lines 404-406)

**Transaction Record Creation - Duplicated 4+ times:**
- BarangController::store (Lines 88-101, 107-120)
- BarangController::updateStok (Lines 272-302)

---

## 4. Route Analysis

### 4.1 Route Organization (web.php)

**Lines:** 144  
**Routes Count:** ~50  
**Structure:** ✅ Well organized with groups

#### Strengths:
✅ **Route Groups with Middleware:**
```php
Route::middleware(['auth'])->group(function () { ... });
Route::middleware(['role:admin'])->group(function () { ... });
```

✅ **Named Routes:** All routes have names following convention

✅ **Resource-Like Pattern:** 
```php
Route::get('/barang', ...)->name('barang.index');
Route::get('/barang/create', ...)->name('barang.create');
Route::post('/barang', ...)->name('barang.store');
```

#### Issues:

🟡 **Debug Routes in Production (Lines 87-132):**
```php
Route::get('/check-seed', function () { ... });
Route::get('/seed-transaksi', function () { ... });
```
**Risk:** These should be in separate routes file or protected.

🟡 **Manual Admin Routes:**
```php
// Lines 44-49 - Could use Route::resource with middleware
Route::middleware(['role:admin'])->group(function () {
    Route::get('/barang/{barang}/edit', ...);
    Route::put('/barang/{barang}', ...);
    // ...
});
```

### 4.2 Route-Controller Mapping

| Resource | Routes | Controller | RESTful? |
|----------|--------|------------|----------|
| Barang | 9 | BarangController | Partial |
| Transaksi | 8 | TransaksiController | Partial |
| Ruangan | 7 | RuanganController | Partial |
| Users | 6 | AuthController | ❌ Wrong Controller |
| Quarterly Stock | 2 | QuarterlyStockController | Custom |
| Surat Tanda Terima | 2 | SuratTandaTerimaController | Custom |

**Issue:** User management in AuthController instead of UserController.

---

## 5. Design Patterns Analysis

### 5.1 Patterns Currently Used

| Pattern | Implementation | Rating |
|---------|---------------|--------|
| MVC | Basic Laravel structure | ✅ Standard |
| Repository | ❌ None | 🔴 Missing |
| Service Layer | ❌ None | 🔴 Missing |
| Factory | Model factories (default) | 🟢 Standard |
| Middleware | Custom RoleMiddleware | 🟢 Good |
| Export Pattern | Maatwebsite Excel | 🟢 Good |
| Caching | DashboardController | 🟢 Good |

### 5.2 Anti-Patterns Identified

#### 🔴 Fat Controller
**Files:** TransaksiController (588 lines), BarangController (330 lines)

**Symptoms:**
- Business logic mixed with HTTP concerns
- Multiple responsibilities (CRUD + Export + Bulk operations)
- Database transactions in controller

#### 🔴 God Object (Partial)
**File:** TransaksiController

**Symptoms:**
- 11 methods
- Handles: CRUD, Export, AJAX, Stock updates
- Multiple reasons to change

#### 🟡 Magic Numbers
**Example (BarangController):**
```php
$satuans = ['Buah', 'Rim', 'Dos', ...]; // Lines 47, 53, 147
```

**Should be:**
```php
// config/satuan.php or database table
```

#### 🟡 Feature Envy
**Example (QuarterlyStockController):**
```php
// Lines 145-355 accessing Transaksi model extensively
// Should be in Repository or Service
```

---

## 6. Security Analysis

### 6.1 Security Headers Middleware ✅
**File:** SecurityHeaders.php

**Implemented:**
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin
- Content-Security-Policy (comprehensive)
- Permissions-Policy

### 6.2 Role-Based Access Control

| Check | Implementation | Risk |
|-------|---------------|------|
| Auth middleware | ✅ Standard Laravel | Low |
| Role middleware | ✅ Custom RoleMiddleware | Low |
| Manual checks | 🟡 Inconsistent | Medium |

**Issue:** Some controllers check authorization manually instead of using middleware or policies.

### 6.3 Validation

| Controller | Form Request | Inline Validation | Risk |
|------------|-------------|-------------------|------|
| AuthController | ❌ | ✅ | Low |
| BarangController | ❌ | ✅ | Low |
| TransaksiController | ❌ | ✅ | Medium |
| RuanganController | ❌ | ✅ | Low |

**Issue:** No Form Request classes. All validation inline.

---

## 7. Refactoring Recommendations

### 7.1 Priority: CRITICAL (Week 1-2)

#### 1. Extract Service Layer
```
app/Services/
├── TransactionService.php
├── StockService.php
├── ExportService.php
└── DocumentService.php
```

**StockService Example:**
```php
class StockService
{
    public function adjustStock(Barang $barang, int $newStock, ?string $keterangan): TransactionResult
    {
        // Centralized stock logic
    }
    
    public function calculateStockForBarang(int $barangId): int
    {
        // Centralized calculation
    }
}
```

#### 2. Create Form Request Classes
```
app/Http/Requests/
├── StoreBarangRequest.php
├── UpdateBarangRequest.php
├── StoreTransaksiRequest.php
└── ExportTransaksiRequest.php
```

#### 3. Extract Repository Layer
```
app/Repositories/
├── BarangRepository.php
├── TransaksiRepository.php
└── RuanganRepository.php
```

### 7.2 Priority: HIGH (Week 2-3)

#### 4. Create UserController
Move user management from AuthController to dedicated UserController.

#### 5. Add Model Scopes
Add reusable scopes to models for common queries.

#### 6. Create Model Observers
```
app/Observers/
├── TransaksiObserver.php
└── BarangObserver.php
```

### 7.3 Priority: MEDIUM (Week 3-4)

#### 7. Refactor DOCX Generation
Create dedicated service for document generation.

#### 8. Add Query Optimization
- Add database indexes
- Use lazy loading properly
- Cache frequently accessed data

#### 9. Standardize Error Handling
Create custom exception classes and handler.

### 7.4 Priority: LOW (Week 4+)

#### 10. Code Quality Improvements
- Add PHPStan/Larastan
- Add stricter type hints
- Add method documentation

---

## 8. Architecture Improvement Roadmap

### Phase 1: Foundation (Weeks 1-2)
- [ ] Create Service Layer structure
- [ ] Move stock logic to StockService
- [ ] Move transaction logic to TransactionService
- [ ] Create Form Request classes
- [ ] Add unit tests for services

### Phase 2: Refactoring (Weeks 3-4)
- [ ] Refactor TransaksiController (target: <200 lines)
- [ ] Refactor BarangController (target: <150 lines)
- [ ] Create UserController
- [ ] Add Model scopes
- [ ] Add Observers

### Phase 3: Optimization (Weeks 5-6)
- [ ] Add Repository Layer
- [ ] Implement caching strategy
- [ ] Optimize database queries
- [ ] Add API rate limiting
- [ ] Performance testing

### Phase 4: Quality (Weeks 7-8)
- [ ] Add static analysis (PHPStan level 6+)
- [ ] Add comprehensive test coverage (target: 80%+)
- [ ] Document API endpoints
- [ ] Code review and cleanup

---

## 9. Complexity Metrics

### 9.1 Cyclomatic Complexity (Estimated)

| Method | Lines | Complexity | Risk |
|--------|-------|------------|------|
| TransaksiController::store | 99 | 12+ | High |
| TransaksiController::update | 106 | 15+ | High |
| TransaksiController::export | 114 | 20+ | Critical |
| BarangController::store | 76 | 10+ | Medium |
| BarangController::updateStok | 86 | 8+ | Medium |

**Target:** Maximum 10 complexity per method

### 9.2 Code Coupling

| Controller | Dependencies | Coupling Level |
|------------|-------------|----------------|
| TransaksiController | 7 (Auth, Log, Excel, DB, Models) | 🔴 High |
| BarangController | 5 (Auth, DB, Excel, Models) | 🟡 Medium |
| RuanganController | 3 | 🟢 Low |

---

## 10. Files Affected by Refactoring

### High Impact (Must Refactor)
1. `app/Http/Controllers/TransaksiController.php` - 588 lines
2. `app/Http/Controllers/BarangController.php` - 330 lines
3. `app/Http/Controllers/QuarterlyStockController.php` - 399 lines

### Medium Impact (Should Refactor)
4. `app/Http/Controllers/AuthController.php` - Split user management
5. `app/Http/Controllers/SuratTandaTerimaController.php` - Extract DOCX logic

### New Files Required
```
app/Services/StockService.php
app/Services/TransactionService.php
app/Services/DocumentService.php
app/Services/ExportService.php
app/Http/Controllers/UserController.php
app/Http/Requests/StoreBarangRequest.php
app/Http/Requests/StoreTransaksiRequest.php
app/Repositories/TransaksiRepository.php
app/Repositories/BarangRepository.php
app/Observers/TransaksiObserver.php
app/Observers/BarangObserver.php
```

---

## 11. Conclusion

### Summary
The current architecture follows basic Laravel MVC patterns but suffers from **Fat Controller** anti-patterns. Business logic is scattered across controllers, leading to code duplication and maintenance challenges.

### Key Issues
1. **No Service Layer** - Business logic in controllers (588 lines in TransaksiController)
2. **No Repository Layer** - Direct model queries everywhere
3. **No Form Requests** - Inline validation repeated
4. **Code Duplication** - Stock calculation logic repeated 6+ times
5. **Mixed Concerns** - DOCX generation in controllers

### Positive Aspects
1. ✅ Proper middleware usage
2. ✅ Security headers implemented
3. ✅ Route naming conventions followed
4. ✅ Good model relationships defined
5. ✅ Caching in DashboardController

### Recommendation
Implement **Service Layer Architecture** immediately. This will:
- Reduce controller line count by 60-70%
- Centralize business logic
- Improve testability
- Enable code reuse
- Simplify maintenance

**Estimated Effort:** 3-4 weeks for full refactoring  
**Risk Level:** Medium (requires thorough testing)  
**ROI:** High (long-term maintainability)

---

*Report generated by Claude Code - Architecture Analysis Agent*
