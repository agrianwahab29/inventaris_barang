# Design Document: Bulk Delete Feature

**Date:** 2025-01-13
**Status:** Approved
**Author:** AI Assistant
**Scope:** Barang, Ruangan, Transaksi, Users

---

## 1. Overview

### 1.1 Problem Statement

Fitur bulk delete (hapus banyak item sekaligus) sudah ada di beberapa menu namun tidak berfungsi dengan baik. Khususnya di menu Barang, ID item yang dipilih tidak terkirim ke server saat tombol "Hapus Dipilih" diklik. Menu Users sama sekali belum memiliki fitur bulk delete.

### 1.2 Goals

- Perbaiki fitur bulk delete di menu Barang
- Pastikan fitur bulk delete berfungsi di menu Ruangan dan Transaksi
- Tambahkan fitur bulk delete di menu Users
- Implementasi toolbar yang sticky di atas (fixed top)
- Konfirmasi hapus yang simple dan jelas
- Pesan hasil yang menggabungkan sukses dan gagal

### 1.3 Non-Goals

- Mengubah tampilan/UX lain yang sudah ada
- Menambah fitur baru selain bulk delete
- Mengubah struktur database

---

## 2. Affected Menus

| Menu | Status | Action |
|------|--------|--------|
| Barang (Data Buku) | Perlu perbaikan | Perbaiki JavaScript dan form |
| Ruangan (Kategori) | Sudah ada, perlu verifikasi | Pastikan toolbar sticky |
| Transaksi (Riwayat) | Sudah ada, perlu verifikasi | Pastikan toolbar sticky |
| Users (Manajemen User) | Belum ada | Tambahkan fitur baru lengkap |

---

## 3. Components

### 3.1 Frontend Components

#### 3.1.1 Checkbox Per Baris

Setiap baris tabel memiliki checkbox untuk memilih item individual.

```html
<input type="checkbox" class="form-check-input item-checkbox" 
       name="ids[]" value="{{ $item->id }}">
```

#### 3.1.2 Select All Header

Checkbox di header tabel untuk memilih semua item di halaman tersebut.

```html
<input type="checkbox" class="form-check-input" id="selectAllHeader">
```

#### 3.1.3 Bulk Toolbar (Sticky Top)

Toolbar yang muncul saat ada item dipilih, fixed di bawah navbar.

```html
<div id="bulkToolbar" class="bulk-toolbar" style="display: none;">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-danger" id="selectedCount">0</span>
            <span>item dipilih</span>
        </div>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin hapus item terpilih?')">
            <i class="fas fa-trash me-1"></i>Hapus Terpilih
        </button>
    </div>
</div>
```

Styling untuk sticky top:

```css
.bulk-toolbar {
    position: sticky;
    top: 60px; /* below navbar */
    z-index: 50;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-radius: 0 0 12px 12px;
    padding: 12px 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.3s ease-out;
}
```

#### 3.1.4 JavaScript Logic

```javascript
// Select all functionality
const selectAllHeader = document.getElementById('selectAllHeader');
const itemCheckboxes = document.querySelectorAll('.item-checkbox');
const bulkToolbar = document.getElementById('bulkToolbar');
const selectedCount = document.getElementById('selectedCount');

function updateBulkToolbar() {
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    selectedCount.textContent = checked;
    bulkToolbar.style.display = checked > 0 ? 'block' : 'none';
    
    // Update select all checkbox state
    if (selectAllHeader) {
        selectAllHeader.checked = checked === itemCheckboxes.length && checked > 0;
    }
}

if (selectAllHeader) {
    selectAllHeader.addEventListener('change', function() {
        itemCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkToolbar();
    });
}

itemCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkToolbar);
});
```

### 3.2 Backend Components

#### 3.2.1 Controller Method Pattern

Setiap controller memiliki method `bulkDelete` dengan signature:

```php
public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);
    
    if (empty($ids)) {
        return back()->with('error', 'Pilih minimal satu item untuk dihapus');
    }

    DB::beginTransaction();
    try {
        $items = Model::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($items as $item) {
            // Check if can delete
            if ($this->canDelete($item)) {
                $item->delete();
                $deletedCount++;
            } else {
                $skippedCount++;
            }
        }

        DB::commit();
        
        $message = $deletedCount . ' item berhasil dihapus';
        if ($skippedCount > 0) {
            $message .= ' (' . $skippedCount . ' item dilewati)'
        }
        
        return redirect()->route('route.index')->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

---

## 4. Implementation Details

### 4.1 Barang (Data Buku)

**File:** `resources/views/barang/index.blade.php`

**Current Issue:**
- Ada dua form (`bulkDeleteForm` dan `bulkDeleteFormHidden`) yang tidak terintegrasi
- Checkbox tidak ada di dalam form manapun
- ID tidak terkirim ke server

**Fix:**
1. Satukan menjadi satu form yang membungkus tabel
2. Pastikan semua checkbox ada di dalam form dengan `name="ids[]"`
3. Hapus form ganda yang tidak perlu
4. Tambahkan toolbar sticky top

**Protection:** Item dengan riwayat transaksi tidak bisa dihapus.

### 4.2 Ruangan (Kategori)

**File:** `resources/views/ruangan/index.blade.php`

**Current Status:** Struktur sudah benar, perlu verifikasi dan styling toolbar.

**Changes:**
1. Tambahkan styling sticky top untuk toolbar
2. Pastikan JavaScript bekerja dengan benar

**Protection:** Ruangan yang masih dipakai di transaksi tidak bisa dihapus.

### 4.3 Transaksi (Riwayat)

**File:** `resources/views/transaksi/index.blade.php`

**Current Status:** Struktur sudah benar, perlu verifikasi dan styling toolbar.

**Changes:**
1. Tambahkan styling sticky top untuk toolbar
2. Pastikan JavaScript bekerja dengan benar

**Protection:** User biasa hanya bisa menghapus transaksi milik sendiri. Admin bisa menghapus semua.

### 4.4 Users (Manajemen User)

**File:** `resources/views/users/index.blade.php`

**Current Status:** Belum ada fitur bulk delete.

**New Implementation:**
1. Tambahkan kolom checkbox di tabel
2. Tambahkan select all header
3. Tambahkan toolbar sticky top
4. Tambahkan form wrapper dengan method DELETE

**Backend:** `app/Http/Controllers/AuthController.php`

**New Route:**
```php
Route::delete('/users/bulk/delete', [AuthController::class, 'bulkDeleteUsers'])
    ->name('users.bulkDelete');
```

**New Method in AuthController:**
```php
public function bulkDeleteUsers(Request $request)
{
    // Only admin can bulk delete
    if (!Auth::user()->isAdmin()) {
        return back()->with('error', 'Hanya admin yang dapat menghapus user');
    }

    $ids = $request->input('ids', []);
    
    if (empty($ids)) {
        return back()->with('error', 'Pilih minimal satu user untuk dihapus');
    }

    DB::beginTransaction();
    try {
        $users = User::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Cannot delete self
            if ($user->id === Auth::id()) {
                $skippedCount++;
                continue;
            }
            $user->delete();
            $deletedCount++;
        }

        DB::commit();
        
        $message = $deletedCount . ' user berhasil dihapus';
        if ($skippedCount > 0) {
            $message .= ' (' . $skippedCount . ' user dilewati karena tidak dapat dihapus)';
        }
        
        return redirect()->route('users.index')->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
```

**Protection:** Tidak bisa menghapus akun sendiri.

---

## 5. File Changes Summary

### Files to Modify

| File | Changes |
|------|---------|
| `resources/views/barang/index.blade.php` | Perbaiki struktur form, toolbar sticky |
| `resources/views/ruangan/index.blade.php` | Styling toolbar sticky |
| `resources/views/transaksi/index.blade.php` | Styling toolbar sticky |
| `resources/views/users/index.blade.php` | Tambahkan bulk delete lengkap |
| `app/Http/Controllers/AuthController.php` | Tambah method bulkDeleteUsers |
| `routes/web.php` | Tambah route users.bulkDelete |

---

## 6. Testing Checklist

- [ ] Barang: Select all checkbox bekerja
- [ ] Barang: Individual checkbox bekerja
- [ ] Barang: Toolbar muncul saat item dipilih
- [ ] Barang: Toolbar sticky di atas
- [ ] Barang: Counter update dengan benar
- [ ] Barang: Hapus berhasil dengan konfirmasi
- [ ] Barang: Item dengan transaksi dilewati dengan pesan
- [ ] Ruangan: Semua fitur sama seperti Barang
- [ ] Transaksi: Semua fitur sama seperti Barang
- [ ] Transaksi: User biasa hanya bisa hapus transaksi sendiri
- [ ] Users: Semua fitur baru bekerja
- [ ] Users: Tidak bisa hapus akun sendiri
- [ ] Semua: Toolbar muncul hanya untuk Admin

---

## 7. Security Considerations

1. **Authorization:** Pastikan hanya Admin yang bisa mengakses bulk delete (kecuali Transaksi untuk transaksi sendiri)
2. **CSRF:** Form menggunakan `@csrf` dan `@method('DELETE')`
3. **Input Validation:** Validasi array `ids` tidak kosong
4. **Ownership Check:** Transaksi - user biasa hanya bisa hapus milik sendiri
5. **Self-delete Protection:** Users - tidak bisa hapus akun sendiri
6. **Foreign Key Protection:** Barang dan Ruangan - cek referensi sebelum hapus

---

## 8. User Experience

### Flow

1. User membuka halaman (Barang/Ruangan/Transaksi/Users)
2. User melihat tabel dengan checkbox di setiap baris
3. User mencentang satu atau beberapa item (atau klik Select All)
4. Toolbar muncul sticky di atas tabel
5. Toolbar menampilkan jumlah item yang dipilih
6. User klik "Hapus Terpilih"
7. Konfirmasi popup muncul: "Yakin hapus X item terpilih?"
8. Jika OK, request dikirim ke server
9. Server memproses dan mengembalikan hasil
10. Halaman refresh dengan pesan sukses/gagal

### Responsive

- Toolbar sticky tetap terlihat saat scroll
- Mobile: Toolbar full-width, tombol tetap accessible
- Touch-friendly: Checkbox dan tombol cukup besar

---

## 9. Future Considerations

None - scope terbatas pada fitur bulk delete saja.