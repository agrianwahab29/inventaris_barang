# Product Specification Required
## Sistem Inventaris Kantor

**Version**: 2.0  
**Last Updated**: April 2026  
**Status**: Production Ready  
**Repository**: https://github.com/agrianwahab29/inventaris_barang

---

## 📋 Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [User Personas & Roles](#2-user-personas--roles)
3. [Functional Requirements](#3-functional-requirements)
4. [Non-Functional Requirements](#4-non-functional-requirements)
5. [Technical Architecture](#5-technical-architecture)
6. [Database Schema](#6-database-schema)
7. [API Specifications](#7-api-specifications)
8. [User Interface Requirements](#8-user-interface-requirements)
9. [Testing Requirements](#9-testing-requirements)
10. [Acceptance Criteria by Feature](#10-acceptance-criteria-by-feature)

---

## 1. Executive Summary

### 1.1 Product Vision
Sistem Inventaris Kantor adalah aplikasi web komprehensif untuk mengelola inventaris barang di lingkungan perkantoran. Sistem ini menyediakan tracking real-time stok barang, pencatatan transaksi masuk/keluar, dan pembuatan laporan stok opname berkala.

### 1.2 Key Value Propositions

| Value Proposition | Description |
|-------------------|-------------|
| **Real-time Tracking** | Monitoring stok barang secara langsung dengan update otomatis |
| **Multi-user Support** | Role-based access control untuk admin dan pengguna biasa |
| **Automated Reports** | Export ke Excel dan Word dengan format profesional |
| **Security First** | Rate limiting, CSRF protection, XSS prevention |
| **Mobile Ready** | Responsive design untuk akses di berbagai perangkat |

### 1.3 Target Users
- **Primary**: Administrator/Staff IT (Full Access)
- **Secondary**: Staff Kantor/Pengguna (Limited Access - transaksi only)

---

## 2. User Personas & Roles

### 2.1 Administrator (Admin)
- **Profile**: Staff IT, Biro Umum, atau admin sistem
- **Goals**: Mengelola master data, mengontrol akses user, audit laporan
- **Permissions**:
  - ✅ Full CRUD untuk Barang
  - ✅ Full CRUD untuk Ruangan
  - ✅ Full CRUD untuk Users
  - ✅ Create/Edit/Delete Transaksi
  - ✅ Generate laporan (Excel & DOCX)
  - ✅ Update stok barang
  - ✅ Bulk operations (delete multiple items)

### 2.2 Pengguna (User)
- **Profile**: Staff kantor yang mengambil/menggunakan barang
- **Goals**: Mencatat pengambilan barang, melihat stok tersedia
- **Permissions**:
  - ✅ View Barang (list & detail)
  - ✅ Create Transaksi (masuk/keluar)
  - ✅ Update stok barang (koreksi)
  - ❌ Tidak bisa create/edit/delete master data (Barang, Ruangan)
  - ❌ Tidak bisa access User Management

### 2.3 Guest (Unauthenticated)
- **Permissions**: Hanya bisa akses /login
- **Redirect**: Auto-redirect ke /login jika akses protected routes

---

## 3. Functional Requirements

### 3.1 Authentication & Authorization Module

#### 3.1.1 Login Functionality
**Endpoint**: `GET|POST /login`

**Acceptance Criteria**:
1. User dapat mengakses halaman login dengan form username dan password
2. Validasi input: username dan password wajib diisi
3. Credential check terhadap database users
4. Jika valid:
   - Session dibuat dengan regenerate token
   - Redirect ke intended URL atau /dashboard
   - Flash welcome message
5. Jika invalid:
   - Kembali ke login page
   - Error message: "Username atau password salah"
   - Input username dipertahankan (old input)
6. Rate limiting: 5 attempts per minute (throttle:5,1)

**Error States**:
- Empty username/password → Validation error
- Invalid credentials → "Username atau password salah"
- Rate limit exceeded → 429 Too Many Requests

#### 3.1.2 Logout Functionality
**Endpoint**: `POST /logout`

**Acceptance Criteria**:
1. Hanya authenticated user yang bisa logout
2. Auth::logout() dipanggil
3. Session invalidated dan regenerate CSRF token
4. Redirect ke /login

#### 3.1.3 Role-Based Access Control (RBAC)
**Middleware**: `auth`, `role:admin`

**Acceptance Criteria**:
1. Semua protected routes menggunakan middleware `auth`
2. Admin-only routes menggunakan middleware `role:admin`
3. Pengguna mencoba akses admin routes → Redirect atau 403 Forbidden
4. Guest mencoba akses protected routes → Redirect ke /login
5. Role check menggunakan $user->role === 'admin'

**Access Control Matrix**:

| Feature | Admin | Pengguna | Guest |
|---------|-------|----------|-------|
| Dashboard | ✅ | ✅ | ❌ |
| Barang List | ✅ | ✅ | ❌ |
| Barang Create | ✅ | ❌ | ❌ |
| Barang Edit | ✅ | ❌ | ❌ |
| Barang Delete | ✅ | ❌ | ❌ |
| Update Stok | ✅ | ✅ | ❌ |
| Transaksi CRUD | ✅ | ✅ | ❌ |
| Ruangan List | ✅ | ✅ | ❌ |
| Ruangan Create/Edit/Delete | ✅ | ❌ | ❌ |
| Laporan Generate | ✅ | ✅ | ❌ |
| User Management | ✅ | ❌ | ❌ |

---

### 3.2 Dashboard Module

**Endpoint**: `GET /` atau `GET /dashboard`

#### 3.2.1 Welcome Banner
**Acceptance Criteria**:
1. Display welcome message dengan nama user yang login
2. Format: "Selamat Datang, [Nama User]!"
3. Role indicator badge (Admin/Pengguna)

#### 3.2.2 Statistics Cards (4 cards)

**Card 1: Total Barang**
- Display: Count total barang di database
- Query: `Barang::count()`
- Icon: Box/package icon
- Color: Primary (blue)

**Card 2: Barang Stok Rendah**
- Display: Count barang dengan `stok <= stok_minimum`
- Query: `Barang::whereColumn('stok', '<=', 'stok_minimum')->count()`
- Icon: Warning icon
- Color: Warning (orange/yellow)
- Badge: Alert indicator

**Card 3: Total Transaksi Hari Ini**
- Display: Count transaksi dengan `tanggal = today`
- Query: `Transaksi::whereDate('tanggal', today())->count()`
- Icon: Clipboard/transaction icon
- Color: Success (green)

**Card 4: Total Transaksi Bulan Ini**
- Display: Count transaksi dengan `tanggal` di bulan ini
- Query: `Transaksi::whereMonth('tanggal', now()->month)->count()`
- Icon: Calendar icon
- Color: Info (cyan)

#### 3.2.3 Quick Actions
**Acceptance Criteria**:
1. Button group dengan 3 primary actions:
   - "Input Barang Masuk" → Link ke /transaksi/create?tipe=masuk
   - "Input Barang Keluar" → Link ke /transaksi/create?tipe=keluar
   - "Cek Stok Rendah" → Link ke /barang?filter=stok_rendah

#### 3.2.4 Recent Transactions
**Acceptance Criteria**:
1. Display 5 transaksi terakhir (order by created_at desc)
2. Columns: Tanggal, Nama Barang, Tipe, Jumlah
3. Badge tipe: Masuk (green), Keluar (red), Masuk_Keluar (yellow)
4. Link "Lihat Semua Transaksi" → /transaksi

#### 3.2.5 Sidebar Navigation
**Acceptance Criteria**:
1. Menu items:
   - Dashboard (active state)
   - Barang → Dropdown: List, Create (admin only)
   - Transaksi → Dropdown: List, Create
   - Ruangan
   - Laporan → Dropdown: Stock Opname, Surat Tanda Terima
   - Users (admin only)
2. Active menu highlighting
3. Collapsible submenu
4. Responsive: Hide on mobile, hamburger menu trigger

---

### 3.3 Barang (Inventory) Management Module

#### 3.3.1 Create Barang (Admin Only)
**Endpoints**: 
- `GET /barang/create` - Form display
- `POST /barang` - Store data

**Form Fields & Validations**:

| Field | Type | Required | Validation Rules |
|-------|------|----------|------------------|
| nama_barang | String | ✅ | Max 255, Unique |
| kategori | String | ✅ | In: ATK,Furniture,Elektronik,Lainnya |
| satuan | String | ✅ | Max 50 |
| stok | Integer | ❌ | Default 0, Min 0 |
| stok_minimum | Integer | ✅ | Min 1 |
| catatan | Text | ❌ | Nullable |

**Acceptance Criteria**:
1. Admin dapat mengakses form create barang
2. Validasi client-side (HTML5) dan server-side (Laravel Validator)
3. Error messages ditampilkan per field
4. On success:
   - Simpan ke database
   - Redirect ke /barang
   - Flash message: "Barang berhasil ditambahkan"
5. Unique validation: nama_barang tidak boleh duplikat

**Error States**:
- Empty required fields → "Field [name] wajib diisi"
- Duplicate nama_barang → "Nama barang sudah digunakan"
- Stok negatif → "Stok tidak boleh negatif"

#### 3.3.2 Read Barang (All Roles)
**Endpoint**: `GET /barang`

**Acceptance Criteria**:
1. **List View** dengan table columns:
   - No (incremental)
   - Nama Barang
   - Kategori (badge)
   - Satuan
   - Stok (dengan color indicator)
   - Stok Minimum
   - Status (Normal/Rendah/Habis)
   - Actions

2. **Stok Color Indicators**:
   - Stok > stok_minimum: Green badge (Normal)
   - Stok <= stok_minimum && stok > 0: Yellow/Orange badge (Rendah)
   - Stok = 0: Red badge (Habis)

3. **Pagination**:
   - 10 items per page
   - Laravel pagination links
   - Current page indicator

4. **Search Functionality**:
   - Input field: "Cari barang..."
   - Search by: nama_barang (LIKE query)
   - Real-time atau on-submit

5. **Filter by Kategori**:
   - Dropdown select: All, ATK, Furniture, Elektronik, Lainnya
   - Apply filter dengan button atau auto-apply

6. **Sort Options**:
   - Default: nama_barang ASC
   - Clickable headers untuk sorting

7. **Export Button** (All roles):
   - Button: "Export Excel"
   - Download: Data_Transaksi_[timestamp].xlsx
   - Library: Maatwebsite/Excel

#### 3.3.3 View Barang Detail
**Endpoint**: `GET /barang/{id}`

**Acceptance Criteria**:
1. Display detail card dengan:
   - Nama Barang (heading)
   - Kategori (badge)
   - Satuan
   - Stok saat ini (dengan color indicator)
   - Stok Minimum
   - Catatan
2. **Transaction History**:
   - List transaksi terkait barang ini
   - 5 transaksi terakhir
   - Link "Lihat semua transaksi" → /transaksi?barang_id={id}
3. **Actions**:
   - Button "Update Stok" (all roles)
   - Button "Edit" (admin only)
   - Button "Delete" (admin only, dengan confirmation)

#### 3.3.4 Update Barang

**A. Full Edit (Admin Only)**
**Endpoints**:
- `GET /barang/{id}/edit` - Edit form
- `PUT /barang/{id}` - Update data

**Acceptance Criteria**:
1. Pre-populate form dengan existing data
2. Validations sama seperti Create
3. Uniqueness validation ignore current ID
4. On success: Redirect ke /barang dengan flash message

**B. Update Stok (All Roles)**
**Endpoint**: `POST /barang/{id}/update-stok`

**Acceptance Criteria**:
1. Modal/popup dengan input field "Stok Baru"
2. Current stok ditampilkan sebagai reference
3. Input stok (integer, min 0)
4. Optional: Alasan perubahan (catatan)
5. On submit:
   - Update stok di database
   - Catat perubahan (opsional: buat transaksi koreksi)
   - Refresh page atau update UI
   - Success message

#### 3.3.5 Delete Barang (Admin Only)
**Endpoint**: `DELETE /barang/{id}`

**Acceptance Criteria**:
1. Confirmation modal wajib:
   - Title: "Konfirmasi Hapus"
   - Message: "Apakah Anda yakin ingin menghapus [nama_barang]?"
   - Buttons: "Batal", "Ya, Hapus" (red)
2. Validation: Tidak bisa delete jika barang punya transaksi terkait
   - Error: "Barang tidak dapat dihapus karena memiliki riwayat transaksi"
3. Soft delete atau hard delete (tergantung implementasi)
4. On success:
   - Redirect ke /barang
   - Flash message: "Barang berhasil dihapus"

#### 3.3.6 Bulk Delete (Admin Only)
**Endpoint**: `DELETE /barang/bulk/delete`

**Acceptance Criteria**:
1. Checkbox di setiap row table
2. "Select All" checkbox di header
3. Counter: "3 item dipilih"
4. Bulk action dropdown: "Hapus Terpilih"
5. Confirmation modal dengan list nama barang yang akan dihapus
6. Error handling: Jika ada barang yang tidak bisa dihapus, skip dan report

---

### 3.4 Transaksi (Transaction) Module

#### 3.4.1 Transaksi Types Definition

**Type 1: Masuk**
- **Purpose**: Pencatatan barang masuk (pembelian, hibah, dll)
- **Stock Impact**: Stok barang bertambah sebesar jumlah_masuk
- **Required Fields**: barang_id, tipe, jumlah_masuk, tanggal, ruangan_id (optional)
- **Use Case**: Pembelian dari supplier, barang retur dari user

**Type 2: Keluar**
- **Purpose**: Pencatatan barang keluar (penggunaan, distribusi)
- **Stock Impact**: Stok barang berkurang sebesar jumlah_keluar
- **Required Fields**: barang_id, tipe, jumlah_keluar, tanggal_keluar, ruangan_id, nama_pengambil, tipe_pengambil
- **Validation**: jumlah_keluar <= stok_tersedia
- **Use Case**: Pengambilan oleh staff, distribusi ke ruangan

**Type 3: Masuk_Keluar**
- **Purpose**: Kombinasi transaksi (tukar tambah, partial retur)
- **Stock Impact**: 
  - Stok bertambah sebesar jumlah_masuk
  - Stok berkurang sebesar jumlah_keluar
  - Net change = jumlah_masuk - jumlah_keluar
- **Required Fields**: Semua field masuk + keluar
- **Use Case**: Tukar tambah barang, retur partial ke supplier

#### 3.4.2 Create Transaksi
**Endpoints**:
- `GET /transaksi/create` - Form display
- `POST /transaksi` - Store data

**Form Fields & Validations**:

| Field | Type | Required | Validation Rules |
|-------|------|----------|------------------|
| barang_id | Integer | ✅ | Exists:barang,id |
| tipe | Enum | ✅ | In: masuk,keluar,masuk_keluar |
| jumlah_masuk | Integer | Conditional | Required if tipe=masuk/masuk_keluar, Min 1 |
| jumlah_keluar | Integer | Conditional | Required if tipe=keluar/masuk_keluar, Min 1, Max:stok |
| tanggal | Date | ✅ | Date format, Not future |
| ruangan_id | Integer | Conditional | Required if tipe=keluar/masuk_keluar, Exists:ruangan,id |
| nama_pengambil | String | Conditional | Required if tipe=keluar/masuk_keluar, Max 255 |
| tipe_pengambil | Enum | Conditional | Required if tipe=keluar/masuk_keluar, In: nama_pengambil,nama_ruangan |
| tanggal_keluar | Date | Conditional | Required if tipe=keluar/masuk_keluar |
| keterangan | Text | ❌ | Nullable |

**Conditional Field Display**:
```javascript
If tipe === 'masuk':
  Show: jumlah_masuk, tanggal
  Hide: jumlah_keluar, nama_pengambil, tipe_pengambil, tanggal_keluar (optional)
  ruangan_id: Optional

If tipe === 'keluar':
  Show: jumlah_keluar, tanggal_keluar, ruangan_id, nama_pengambil, tipe_pengambil
  Hide: jumlah_masuk
  Validation: jumlah_keluar <= current_stok

If tipe === 'masuk_keluar':
  Show: All fields
  Validation: jumlah_keluar <= (current_stok + jumlah_masuk)
```

**AJAX Integration**:
1. **Barang Select**:
   - Library: Select2 atau custom AJAX dropdown
   - Endpoint: Search barang by nama_barang
   - On select: Fetch barang info via `GET /api/barang/{id}/info`
   
2. **Stok Info Display**:
   - Display current stok setelah select barang
   - Update real-time saat jumlah diubah
   - Warning jika jumlah_keluar > stok

3. **Ruangan Select**:
   - Dropdown: List semua ruangan
   - On select (for keluar): Auto-fill nama_pengambil jika tipe_pengambil = nama_ruangan

**Acceptance Criteria**:
1. User dapat memilih tipe transaksi (radio button atau select)
2. Form fields dynamic sesuai tipe
3. Barang dapat dicari via AJAX dropdown
4. Stok info ditampilkan real-time
5. Validasi jumlah_keluar tidak melebihi stok
6. On submit:
   - Save transaksi ke database
   - Update stok barang (otomatis)
   - Redirect ke /transaksi dengan flash message
   - Display recalculated stok

**Error States**:
- Jumlah_keluar > stok → "Jumlah keluar melebihi stok tersedia (X unit)"
- Empty barang_id → "Pilih barang terlebih dahulu"
- Empty required fields → "[Field] wajib diisi"
- Invalid date → "Format tanggal tidak valid"

#### 3.4.3 Read Transaksi
**Endpoint**: `GET /transaksi`

**Acceptance Criteria**:
1. **Table Columns**:
   - Tanggal (formatted: d/m/Y)
   - Nama Barang
   - Tipe (badge: Masuk=green, Keluar=red, Masuk_Keluar=yellow)
   - Jumlah Masuk
   - Jumlah Keluar
   - Sisa Stok (setelah transaksi)
   - Ruangan (nama_ruangan atau "-")
   - Pengambil (formatted: nama/ruangan atau "-")
   - Petugas (user yang input)
   - Aksi (View, Edit, Delete)

2. **Filters**:
   - Date range: Dari - Sampai (datepickers)
   - Tipe: All, Masuk, Keluar, Masuk_Keluar
   - Barang: Search atau dropdown
   - Ruangan: Dropdown
   
3. **Sort**:
   - Default: tanggal DESC (terbaru dulu)
   - Clickable headers

4. **Pagination**: 10 items per page

5. **Export**: Button "Export Excel" (all roles)

#### 3.4.4 View Transaksi Detail
**Endpoint**: `GET /transaksi/{id}`

**Acceptance Criteria**:
1. Card dengan detail lengkap:
   - Nomor Transaksi (ID atau custom format)
   - Tanggal Transaksi
   - Nama Barang
   - Tipe (badge besar)
   - Jumlah Masuk (atau "-")
   - Jumlah Keluar (atau "-")
   - Stok Sebelum
   - Stok Setelah
   - Ruangan (jika ada)
   - Nama Pengambil (jika ada)
   - Keterangan
   - Petugas Input
   - Timestamp

2. **Actions**:
   - Button "Edit" (owner atau admin)
   - Button "Delete" (owner atau admin, dengan confirmation)
   - Button "Kembali ke List"

#### 3.4.5 Update Transaksi
**Endpoints**:
- `GET /transaksi/{id}/edit`
- `PUT /transaksi/{id}`

**Business Rules**:
1. **Stock Recalculation**:
   - Restore stok ke kondisi sebelum transaksi
   - Apply perubahan jumlah
   - Update stok barang dengan nilai baru
   
2. **Formula**:
   ```
   // Restore old
   stok_barang = stok_barang - jumlah_masuk_lama + jumlah_keluar_lama
   
   // Apply new
   stok_barang = stok_barang + jumlah_masuk_baru - jumlah_keluar_baru
   ```

3. **Validation**:
   - Same as Create Transaksi
   - Check new totals won't cause negative stock

**Acceptance Criteria**:
1. Pre-populate form dengan existing data
2. Show original values sebagai reference
3. Validasi stok calculation
4. On success:
   - Update transaksi record
   - Recalculate dan update stok barang
   - Redirect ke /transaksi dengan flash message
   - Log perubahan (opsional)

#### 3.4.6 Delete Transaksi
**Endpoint**: `DELETE /transaksi/{id}`

**Business Rules**:
1. **Stock Rollback**:
   - Restore stok ke kondisi sebelum transaksi
   - Formula: `stok_barang = stok_barang - jumlah_masuk + jumlah_keluar`

2. **Permission**:
   - User dapat delete transaksi mereka sendiri
   - Admin dapat delete semua transaksi
   - Soft delete atau hard delete (tergantung policy)

**Acceptance Criteria**:
1. Confirmation modal dengan warning tentang stok rollback
2. On confirm:
   - Rollback stok barang
   - Delete transaksi record
   - Redirect ke /transaksi
   - Flash message: "Transaksi berhasil dihapus"

#### 3.4.7 Bulk Delete Transaksi
**Endpoint**: `DELETE /transaksi/bulk/delete`

**Acceptance Criteria**:
1. Checkbox selection sama seperti Barang
2. Warning: "X transaksi akan dihapus. Stok barang akan dikembalikan ke kondisi sebelum transaksi."
3. Confirmation dengan list transaksi (tanggal + nama barang)
4. Proses delete satu per satu dengan stock rollback
5. Report: "10 dari 12 transaksi berhasil dihapus. 2 gagal: [reasons]"

---

### 3.5 Ruangan (Room/Location) Module

#### 3.5.1 Create Ruangan (Admin Only)
**Endpoints**: `GET /ruangan/create`, `POST /ruangan`

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| nama_ruangan | String | ✅ | Unique, Max 255 |
| keterangan | Text | ❌ | Nullable |

**Acceptance Criteria**:
1. Admin dapat akses form create
2. Validasi uniqueness nama_ruangan
3. On success: Redirect ke /ruangan dengan flash message

#### 3.5.2 Read Ruangan (All Roles)
**Endpoint**: `GET /ruangan`

**Acceptance Criteria**:
1. Table columns:
   - No
   - Nama Ruangan
   - Keterangan
   - Jumlah Transaksi (count transaksi dengan ruangan ini)
   - Aksi (View, Edit-Admin, Delete-Admin)

2. Pagination: 10 per page

3. Search: By nama_ruangan

#### 3.5.3 Update Ruangan (Admin Only)
**Endpoints**: `GET /ruangan/{id}/edit`, `PUT /ruangan/{id}`

**Acceptance Criteria**:
1. Pre-populate form
2. Uniqueness validation (ignore current ID)
3. On success: Redirect ke /ruangan

#### 3.5.4 Delete Ruangan (Admin Only)
**Endpoint**: `DELETE /ruangan/{id}`

**Acceptance Criteria**:
1. Validation: Tidak bisa delete jika ada transaksi terkait
   - Error: "Ruangan tidak dapat dihapus karena digunakan dalam X transaksi"
2. Confirmation modal
3. On success: Redirect dengan flash message

---

### 3.6 Quarterly Stock Opname Module

#### 3.6.1 Generate Laporan
**Endpoints**: `GET /quarterly-stock`, `POST /quarterly-stock/export`

**Input Parameters**:
| Parameter | Type | Required | Options |
|-----------|------|----------|---------|
| quarter | Enum | ✅ | Q1, Q2, Q3, Q4 |
| year | Integer | ✅ | 2024, 2025, etc |

**Report Content**:
- Header: Kode, Nama Barang, Satuan, Stok Awal Periode, Masuk, Keluar, Stok Akhir, Keterangan
- Summary per kategori
- Timestamp generate
- Optional: Kop surat instansi

**Output Format**: DOCX (Word Document)
**Library**: PhpOffice/PhpWord

**Acceptance Criteria**:
1. Form input quarter dan year
2. Preview data sebelum export (table)
3. Button "Export DOCX"
4. Download file dengan naming: `Stock_Opname_[Q]__[Year].docx`
5. File dapat dibuka di Microsoft Word/WPS/LibreOffice

---

### 3.7 Surat Tanda Terima Module

#### 3.7.1 Generate Surat
**Endpoints**: `GET /surat-tanda-terima`, `GET /surat-tanda-terima/generate`

**Form Fields**:
| Field | Type | Required |
|-------|------|----------|
| nomor_surat | String | ✅ |
| tanggal | Date | ✅ |
| dari | String | ✅ (Pengirim) |
| kepada | String | ✅ (Penerima) |
| barang_list | Array/Text | ✅ (List barang yang diterima) |
| keterangan | Text | ❌ |

**Output Format**: DOCX
**Template**: Surat resmi dengan kop surat kantor

**Acceptance Criteria**:
1. Form dengan semua field
2. Barang list: Textarea dengan format atau repeater field
3. Preview sebelum generate
4. Button "Generate DOCX"
5. Download file: `Tanda_Terima_[Nomor]_[Tanggal].docx`
6. Format surat sesuai standar administrasi kantor

---

### 3.8 User Management Module (Admin Only)

#### 3.8.1 Create User
**Endpoints**: `GET /users/create`, `POST /users`

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | String | ✅ | Max 255 |
| username | String | ✅ | Unique, Max 255 |
| email | Email | ✅ | Unique, Valid email |
| password | Password | ✅ | Min 6 chars |
| role | Enum | ✅ | In: admin, pengguna |

**Acceptance Criteria**:
1. Admin dapat akses form create user
2. Password di-hash dengan bcrypt saat save
3. Validasi unique username dan email
4. Throttle: 10 attempts per minute
5. On success: Redirect ke /users dengan flash message

#### 3.8.2 Read Users
**Endpoint**: `GET /users`

**Acceptance Criteria**:
1. Table columns:
   - No
   - Nama
   - Username
   - Email
   - Role (badge: admin=primary, pengguna=secondary)
   - Dibuat Pada
   - Aksi (Edit, Delete)

2. Filters: By role
3. Pagination: 10 per page
4. Search: By name/username/email

#### 3.8.3 Update User
**Endpoints**: `GET /users/{id}/edit`, `PUT /users/{id}`

**Acceptance Criteria**:
1. Pre-populate form (kecuali password)
2. Password field: Kosongkan = tidak ubah password
3. Uniqueness validation (ignore current ID)
4. On success: Redirect dengan flash message

#### 3.8.4 Delete User
**Endpoint**: `DELETE /users/{id}`

**Critical Validation**:
1. **TIDAK BOLEH delete diri sendiri**
   - Check: `if ($user->id === Auth::id())`
   - Error: "Anda tidak dapat menghapus akun Anda sendiri"

2. **Check transaksi terkait** (opsional):
   - Jika user punya transaksi → Restrict delete atau Cascade
   - Error: "User memiliki riwayat transaksi, tidak dapat dihapus"

**Acceptance Criteria**:
1. Confirmation modal
2. Validation checks
3. On success: Redirect dengan flash message

#### 3.8.5 Bulk Delete Users
**Endpoint**: `DELETE /users/bulk/delete`

**Acceptance Criteria**:
1. Checkbox selection
2. Validation: Tidak boleh delete diri sendiri (exclude dari list)
3. Confirmation dengan list users
4. Proses delete satu per satu
5. Report hasil

---

## 4. Non-Functional Requirements

### 4.1 Performance Requirements

| Metric | Target | Measurement |
|--------|--------|-------------|
| Page Load Time | < 3 detik | First Contentful Paint |
| Time to Interactive | < 5 detik | Lighthouse score |
| AJAX Response | < 1 detik | API endpoint response time |
| Export 1000 records | < 10 detik | Excel/Word generation time |
| Database Query | < 500ms | Eloquent query execution |
| Concurrent Users | 10+ users | Load testing |

### 4.2 Security Requirements

#### Authentication & Authorization
- Session-based authentication dengan Laravel Auth
- CSRF token pada semua forms (POST, PUT, DELETE)
- Role middleware untuk proteksi route admin
- Throttling pada:
  - Login: 5 attempts per minute
  - User Create: 10 attempts per minute
- Session timeout: 120 menit (default Laravel)
- Session regenerate setelah login

#### Data Protection
- Password hashing: bcrypt dengan cost 12
- SQL injection prevention via Eloquent ORM (parameterized queries)
- XSS prevention via Blade `{{ }}` escaping
- No raw SQL dengan user input
- No sensitive data di URL parameters
- Secure cookies (HttpOnly, Secure flag jika HTTPS)

#### Input Validation
- Server-side validation wajib untuk semua input
- Client-side validation sebagai UX enhancement
- Sanitasi input untuk prevent injection
- File upload validation (type, size) jika ada

### 4.3 UI/UX Requirements

#### Layout Structure
```
┌─────────────────────────────────────────┐
│  Header (Logo, User Profile, Logout)   │
├──────────┬──────────────────────────────┤
│          │                              │
│ Sidebar  │     Main Content Area      │
│ (Nav)    │     (Dynamic Content)        │
│          │                              │
│          │                              │
├──────────┴──────────────────────────────┤
│  Footer (Copyright, Version)           │
└─────────────────────────────────────────┘
```

#### Responsive Breakpoints
- **Desktop** (≥992px): Full sidebar, 4-column stats, full table
- **Tablet** (768px-991px): Collapsible sidebar, 2-column stats, scrollable table
- **Mobile** (<768px): Hamburger menu, 1-column layout, card-based list

#### Components & Styling
- **Framework**: Bootstrap 5.x
- **Icons**: Font Awesome atau Bootstrap Icons
- **Colors**:
  - Primary: #007bff (Blue)
  - Success: #28a745 (Green) - untuk masuk
  - Danger: #dc3545 (Red) - untuk keluar/hapus
  - Warning: #ffc107 (Yellow) - untuk alert stok rendah
  - Info: #17a2b8 (Cyan)
- **Typography**: System fonts (Segoe UI, Roboto, sans-serif)
- **Animations**: Fade-in untuk modal, slide untuk sidebar

#### User Feedback
- **Toast Notifications**: Success (green), Error (red), Warning (yellow)
- **Loading States**: Spinner pada AJAX calls, button disabled saat submit
- **Form Validation**: Inline errors dengan red border dan message
- **Confirmation Modals**: Untuk delete actions
- **Empty States**: Illustration/message saat data kosong

### 4.4 Browser Compatibility

| Browser | Minimum Version | Support Level |
|---------|----------------|---------------|
| Chrome | 90+ | Primary (Full) |
| Firefox | 88+ | Full |
| Edge | 90+ | Full |
| Safari | 14+ | Full |
| Chrome Android | 90+ | Full |
| Safari iOS | 14+ | Full |

### 4.5 Database Requirements

- **Engine**: MySQL 5.7+ atau MariaDB 10.3+
- **Charset**: utf8mb4_unicode_ci
- **Foreign Keys**: ON DELETE RESTRICT untuk transaksi-barang
- **Indexes**: 
  - barang.nama_barang (untuk search)
  - transaksi.tanggal (untuk filter)
  - transaksi.barang_id (untuk join)
  - users.username dan email (unique)

---

## 5. Technical Architecture

### 5.1 Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend** | PHP | 7.4+ |
| **Framework** | Laravel | 8.x |
| **Frontend** | Blade Templates | - |
| **CSS Framework** | Bootstrap | 5.x |
| **JavaScript** | Vanilla JS + jQuery (opsional) | ES6 |
| **Database** | MySQL/MariaDB | 5.7+/10.3+ |
| **Web Server** | Apache/Nginx | Latest |
| **Testing** | PHPUnit + Playwright | 9.x + 1.59+ |
| **Export Excel** | Maatwebsite/Excel | 3.1+ |
| **Export Word** | PhpOffice/PhpWord | 0.18+ |

### 5.2 Application Structure

```
app/
├── Console/Commands/      # Artisan commands
├── Exports/               # Excel export classes
├── Http/
│   ├── Controllers/       # Request handlers
│   ├── Middleware/        # RoleMiddleware, etc
│   └── Requests/          # Form requests (validation)
├── Models/                # Eloquent models
│   ├── Barang.php
│   ├── Transaksi.php
│   ├── Ruangan.php
│   └── User.php
└── Providers/             # Service providers

config/                    # Configuration files
database/
├── migrations/            # Schema migrations
├── factories/             # Model factories (testing)
└── seeders/               # Database seeders
resources/
├── views/                 # Blade templates
│   ├── layouts/           # Master layouts
│   ├── barang/            # Barang views
│   ├── transaksi/         # Transaksi views
│   └── ...
├── js/                    # JavaScript files
└── css/                   # CSS/SASS files
routes/
└── web.php               # Web routes

tests/
├── e2e/                  # Playwright E2E tests
├── Feature/              # Laravel Feature tests
└── Unit/                 # Unit tests
```

### 5.3 Key Dependencies

```json
{
  "require": {
    "php": "^7.4",
    "laravel/framework": "^8.0",
    "maatwebsite/excel": "^3.1",
    "phpoffice/phpword": "^0.18",
    "guzzlehttp/guzzle": "^7.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.3",
    "facade/ignition": "^2.5",
    "fakerphp/faker": "^1.9"
  }
}
```

---

## 6. Database Schema

### 6.1 Entity Relationship Diagram

```
[Users] 1 ────────< [Transaksi] >─────── 1 [Barang]
   │                     │
   │                     >
   │                1 [Ruangan]
   │
   <
[Transaksi] (created by user)
```

### 6.2 Table Definitions

#### users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pengguna') DEFAULT 'pengguna',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role)
);
```

#### barang
```sql
CREATE TABLE barang (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) UNIQUE NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    satuan VARCHAR(50) NOT NULL,
    stok INT DEFAULT 0,
    stok_minimum INT NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kategori (kategori),
    INDEX idx_stok (stok)
);
```

#### ruangan
```sql
CREATE TABLE ruangan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_ruangan VARCHAR(255) UNIQUE NOT NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### transaksi
```sql
CREATE TABLE transaksi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    barang_id BIGINT UNSIGNED NOT NULL,
    tipe ENUM('masuk', 'keluar', 'masuk_keluar') NOT NULL,
    jumlah INT NULL,
    jumlah_masuk INT DEFAULT 0,
    jumlah_keluar INT DEFAULT 0,
    stok_sebelum INT NOT NULL,
    stok_setelah_masuk INT NULL,
    tanggal DATE NOT NULL,
    ruangan_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    sisa_stok INT NOT NULL,
    nama_pengambil VARCHAR(255) NULL,
    tipe_pengambil ENUM('nama_pengambil', 'nama_ruangan') NULL,
    tanggal_keluar DATE NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE RESTRICT,
    FOREIGN KEY (ruangan_id) REFERENCES ruangan(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_tanggal (tanggal),
    INDEX idx_tipe (tipe),
    INDEX idx_barang_id (barang_id),
    INDEX idx_user_id (user_id)
);
```

---

## 7. API Specifications

### 7.1 AJAX Endpoints

#### GET /api/barang/{id}/info
**Purpose**: Mendapatkan info barang untuk form transaksi

**Request**:
```http
GET /api/barang/123/info
Accept: application/json
```

**Response**:
```json
{
  "id": 123,
  "nama_barang": "Kertas A4",
  "satuan": "Rim",
  "stok": 50,
  "stok_minimum": 10,
  "is_stok_rendah": false
}
```

**Status Codes**:
- 200: Success
- 404: Barang not found
- 401: Unauthorized

#### GET /api/transactions/check-updates
**Purpose**: Check untuk real-time updates (polling)

**Request**:
```http
GET /api/transactions/check-updates?last_check=2026-04-07T10:00:00Z
```

**Response**:
```json
{
  "has_updates": true,
  "new_count": 3,
  "last_update": "2026-04-07T10:30:00Z"
}
```

### 7.2 Export Endpoints

#### GET /barang/export
**Purpose**: Export daftar barang ke Excel

**Response**: Binary Excel file (download)

#### GET /transaksi/export
**Purpose**: Export transaksi ke Excel dengan filter yang aktif

**Query Parameters**:
- `tipe` (optional): Filter by tipe
- `tanggal_dari` (optional): Start date
- `tanggal_sampai` (optional): End date

**Response**: Binary Excel file (download)

#### POST /quarterly-stock/export
**Purpose**: Generate laporan stok opname DOCX

**Request Body**:
```json
{
  "quarter": "Q1",
  "year": 2026
}
```

**Response**: Binary DOCX file (download)

#### GET /surat-tanda-terima/generate
**Purpose**: Generate surat tanda terima DOCX

**Query Parameters**:
- `nomor_surat`
- `tanggal`
- `dari`
- `kepada`
- `barang_list`
- `keterangan`

**Response**: Binary DOCX file (download)

---

## 8. User Interface Requirements

### 8.1 Navigation Structure

```
Dashboard
├── Icon: fas fa-tachometer-alt
├── URL: /

Barang
├── Icon: fas fa-box
├── URL: /barang
├── Children:
│   ├── List Barang (/barang)
│   └── Tambah Barang (/barang/create) [Admin only]

Transaksi
├── Icon: fas fa-exchange-alt
├── URL: /transaksi
├── Children:
│   ├── List Transaksi (/transaksi)
│   └── Tambah Transaksi (/transaksi/create)

Ruangan
├── Icon: fas fa-door-open
├── URL: /ruangan

Laporan
├── Icon: fas fa-file-alt
├── Children:
│   ├── Stock Opname (/quarterly-stock)
│   └── Surat Tanda Terima (/surat-tanda-terima)

Users [Admin only]
├── Icon: fas fa-users
├── URL: /users
```

### 8.2 Common UI Components

#### Data Table Component
- Pagination (10 items default)
- Search input
- Filter dropdowns
- Sortable headers (click to sort)
- Checkbox column (for bulk actions)
- Action buttons column (View, Edit, Delete)
- Empty state illustration

#### Form Component
- Label dengan required indicator (*)
- Input dengan validation states (valid/invalid)
- Error messages inline
- Help text (optional)
- Submit button dengan loading state
- Cancel/Back button

#### Modal Component
- Header dengan title dan close button
- Body content (form atau confirmation message)
- Footer dengan action buttons
- Backdrop click to close (opsional)
- Size variants: sm, md, lg

#### Toast Notification Component
- Position: Top-right atau bottom-right
- Types: Success (green), Error (red), Warning (yellow), Info (blue)
- Auto-dismiss: 5 detik
- Close button manual
- Icon indicator

---

## 9. Testing Requirements

### 9.1 Testing Strategy

| Test Type | Tool | Coverage Target | Priority |
|-----------|------|-----------------|----------|
| **Unit Tests** | PHPUnit | 70%+ | High |
| **Feature Tests** | PHPUnit | Critical paths | High |
| **E2E Tests** | Playwright | Core user flows | Critical |
| **API Tests** | Postman/Playwright | All AJAX endpoints | Medium |
| **Security Tests** | Manual/OWASP ZAP | OWASP Top 10 | Medium |
| **Performance Tests** | Lighthouse | Score > 90 | Medium |

### 9.2 Critical Test Scenarios

#### Scenario 1: Authentication Flow
```gherkin
Feature: Authentication
  Scenario: Successful login
    Given User is on login page
    When User enters valid username "admin" and password "admin123"
    And User clicks Login button
    Then User should be redirected to Dashboard
    And Welcome message should display username
    
  Scenario: Invalid credentials
    Given User is on login page
    When User enters invalid credentials
    And User clicks Login button
    Then Error message "Username atau password salah" should display
    And User should remain on login page
```

#### Scenario 2: Transaksi Masuk
```gherkin
Feature: Transaksi Barang Masuk
  Scenario: Create transaksi masuk increases stock
    Given Barang "Kertas A4" has stock 10
    And User is logged in as admin
    When User creates transaksi masuk for "Kertas A4" with quantity 20
    Then Transaksi should be saved successfully
    And Stock of "Kertas A4" should become 30
    And Transaksi should appear in transaction list
```

#### Scenario 3: Stok Validation
```gherkin
Feature: Stok Validation
  Scenario: Prevent negative stock on keluar
    Given Barang "Pulpen" has stock 5
    When User tries to create transaksi keluar with quantity 10
    Then Error message "Stok tidak mencukupi" should display
    And Transaksi should not be saved
    And Stock should remain 5
```

#### Scenario 4: Role-Based Access
```gherkin
Feature: Role-Based Access Control
  Scenario: Pengguna cannot create barang
    Given User is logged in as "pengguna"
    When User tries to access /barang/create
    Then User should be redirected to Dashboard
    Or User should see 403 Forbidden page
```

### 9.3 E2E Test Coverage

| Module | Test Count | Priority |
|--------|------------|----------|
| Authentication | 8 | Critical |
| Dashboard | 6 | High |
| Barang | 10 | Critical |
| Transaksi | 12 | Critical |
| Ruangan | 5 | Medium |
| Laporan | 4 | Medium |
| User Management | 6 | Medium |

---

## 10. Acceptance Criteria by Feature

### 10.1 Authentication Module - Acceptance Criteria

| ID | Criteria | Priority |
|----|----------|----------|
| AUTH-001 | User dapat login dengan username dan password yang valid | Critical |
| AUTH-002 | Sistem menolak login dengan kredensial invalid | Critical |
| AUTH-003 | Session di-maintain setelah login (page refresh) | High |
| AUTH-004 | User dapat logout dan session dihapus | Critical |
| AUTH-005 | Rate limiting 5 attempts per minute pada login | Medium |
| AUTH-006 | Redirect ke login jika guest akses protected route | Critical |
| AUTH-007 | Admin dapat akses semua routes | Critical |
| AUTH-008 | Pengguna diblokir dari admin-only routes | Critical |

### 10.2 Barang Module - Acceptance Criteria

| ID | Criteria | Priority |
|----|----------|----------|
| BAR-001 | Admin dapat create barang dengan valid data | Critical |
| BAR-002 | System validasi unique nama_barang | Critical |
| BAR-003 | Display list barang dengan pagination | High |
| BAR-004 | Search barang by nama berfungsi | High |
| BAR-005 | Filter by kategori berfungsi | Medium |
| BAR-006 | Stok color indicators (normal/rendah/habis) | Medium |
| BAR-007 | Update stok berfungsi untuk semua role | High |
| BAR-008 | Edit barang (admin only) | High |
| BAR-009 | Delete barang dengan confirmation (admin only) | High |
| BAR-010 | Export Excel berfungsi | Medium |
| BAR-011 | Bulk delete dengan checkbox (admin only) | Low |
| BAR-012 | Validasi stok tidak negatif | Critical |

### 10.3 Transaksi Module - Acceptance Criteria

| ID | Criteria | Priority |
|----|----------|----------|
| TRX-001 | Create transaksi masuk increases stock | Critical |
| TRX-002 | Create transaksi keluar decreases stock | Critical |
| TRX-003 | Create transaksi masuk_keluar dengan kombinasi | Critical |
| TRX-004 | Validasi jumlah_keluar <= stok_tersedia | Critical |
| TRX-005 | AJAX fetch barang info berfungsi | High |
| TRX-006 | Form fields dynamic sesuai tipe transaksi | High |
| TRX-007 | Display transaksi list dengan filter | High |
| TRX-008 | Edit transaksi dengan recalculate stock | Medium |
| TRX-009 | Delete transaksi dengan stock rollback | Medium |
| TRX-010 | Export transaksi Excel berfungsi | Medium |
| TRX-011 | Ruangan dapat dipilih untuk transaksi keluar | High |
| TRX-012 | Nama pengambil tercatat dengan format benar | Medium |

### 10.4 Ruangan Module - Acceptance Criteria

| ID | Criteria | Priority |
|----|----------|----------|
| RUA-001 | Admin dapat create ruangan | High |
| RUA-002 | Validasi unique nama_ruangan | Critical |
| RUA-003 | Display list ruangan dengan jumlah transaksi | Medium |
| RUA-004 | Edit ruangan (admin only) | Medium |
| RUA-005 | Delete ruangan dengan check transaksi terkait | High |

### 10.5 Laporan Module - Acceptance Criteria

| ID | Criteria | Priority |
|----|----------|----------|
| LAP-001 | Generate quarterly stock opname DOCX | High |
| LAP-002 | Generate surat tanda terima DOCX | Medium |
| LAP-003 | Format laporan sesuai standar | Medium |
| LAP-004 | File dapat di-download dan dibuka | High |

### 10.6 User Management - Acceptance Criteria

| ID | Criteria | Priority |
|----|----------|----------|
| USR-001 | Admin dapat create user | High |
| USR-002 | Validasi unique username dan email | Critical |
| USR-003 | Password di-hash dengan bcrypt | Critical |
| USR-004 | Edit user dengan role change | Medium |
| USR-005 | Delete user dengan self-delete prevention | Critical |
| USR-006 | Bulk delete users | Low |

---

## Appendices

### A. Glossary

| Term | Definition |
|------|------------|
| **ATK** | Alat Tulis Kantor |
| **CRUD** | Create, Read, Update, Delete |
| **E2E** | End-to-End testing |
| **RBAC** | Role-Based Access Control |
| **DOCX** | Microsoft Word Document format |
| **Stok Opname** | Physical stock counting process |

### B. Document Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | April 2026 | AI Assistant | Initial PRD creation for TestSprite |
| 2.0 | April 2026 | AI Assistant | Complete rewrite with detailed acceptance criteria |

### C. Related Documents

- `README.md` - Project overview and setup guide
- `AGENTS.md` - AI agent context and workflow guide
- `docs/IMPLEMENTATION_PLAN.md` - Technical implementation details
- `tests/e2e/` - Playwright test specifications

---

*End of Product Specification Required*
*Ready for TestSprite MCP Testing*
