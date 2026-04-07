# Export Functionality Testing Guide

**Date**: {{ date('Y-m-d') }}  
**Author**: AI Assistant  
**Purpose**: Comprehensive testing guide for all 7 export types on Transaksi page

---

## 🎯 Overview

This document provides step-by-step testing instructions for the export functionality at http://127.0.0.1:8000/transaksi. All 7 export types should generate valid Excel files (.xlsx).

---

## ✅ Prerequisites

Before testing, ensure:

1. **Database has transactions**
   - Minimum 10 transactions in database
   - Transactions spanning multiple dates, months, and years
   - Mix of barang masuk and barang keluar

2. **PHP Extensions installed**
   ```bash
   php -m | grep -E "(zip|xml|gd)"
   ```
   Expected output:
   - zip
   - xml
   - gd (or gd2)

3. **Storage permissions**
   ```bash
   # Windows (Laragon)
   # No special commands needed, should be writable by default
  
  # Linux/Mac
   chmod -R 775 storage/app
  ```

4. **Browser**
   - Modern browser (Chrome, Firefox, Edge)
   - JavaScript enabled
  - Popup blocker disabled or allowed for localhost

---

## 🧪 Test Cases

### Test 1: Export Semua Data (Export Type: `all`)

**Purpose**: Export all transactions in database

**Steps**:
1. Navigate to http://127.0.0.1:8000/transaksi
2. Click "Export" button (green button)
3. Export modal opens
4. Select "Semua Data" (should be selected by default)
5. (Optional) Select user filter if admin
6. Click "Export" button in modal footer
7. File should download automatically

**Expected Result**:
- ✅ File downloads as `Data_Transaksi_YYYY-MM-DD_HH-mm-ss.xlsx`
- ✅ File opens in Excel without errors
- ✅ Contains all transactions from database
- ✅ Header row has proper formatting (bold, white text, blue background)
- ✅ Data rows have alternating colors

**Test Data Needed**: Any transactions in database

**Edge Cases**:
- Empty database → Should show error message: "Tidak ada data transaksi untuk diexport"
- Large dataset (>1000 rows) → Should complete within 60 seconds

---

### Test 2: Export Rentang Tanggal (Export Type: `range`)

**Purpose**: Export transactions within date range

**Steps**:
1. Open Export modal
2. Select "Rentang Tanggal" card
3. Select "Dari Tanggal" from dropdown or manually input date
4. Select "Sampai Tanggal" from dropdown or manually input date
5. Click "Export"

**Expected Result**:
- ✅ File downloads
- ✅ Only transactions within date range included
- ✅ Date dari <= Date sampai (validated)

**Test Data Needed**:
- Transactions spanning multiple dates
- Test with various ranges:
  - Single day: 2024-01-15 to 2024-01-15
  - One week: 2024-01-01 to 2024-01-07
  - One month: 2024-01-01 to 2024-01-31

**Edge Cases**:
- dari > sampai → Error message: "Tanggal dari harus lebih kecil atau sama dengan tanggal sampai"
- Empty dari/sampai → Error message: "Pilih tanggal dari dan sampai"
- No transactions in range → Error message: "Tidak ada data transaksi untuk kriteria yang dipilih"

---

### Test 3: Export Pilih Tanggal (Export Type: `dates`)

**Purpose**: Export transactions from multiple specific dates

**Steps**:
1. Open Export modal
2. Select "Pilih Tanggal" card
3. Select date from dropdown
4. Click "Tambah" button
5. Repeat for multiple dates
6. Date chips appear showing selected dates
7. Click "Export"

**Expected Result**:
- ✅ File downloads
- ✅ Only transactions from selected dates included
- ✅ Multiple dates work correctly
- ✅ Date chips can be removed by clicking X

**Test Data Needed**:
- Transactions on specific dates
- Test with 1, 3, and 5+ dates

**Edge Cases**:
- No dates selected → Error message: "Pilih minimal satu tanggal"
- Duplicate date selection → Prevented by JavaScript
- Date with no transactions → File contains 0 rows (still downloads)

---

### Test 4: Export Per Tahun (Export Type: `year`)

**Purpose**: Export all transactions from a specific year

**Steps**:
1. Open Export modal
2. Select "Per Tahun" card
3. Year dropdown shows years with transaction data
4. Select a year
5. Click "Export"

**Expected Result**:
- ✅ File downloads
- ✅ Only transactions from selected year included
- ✅ Year dropdown only shows years that have data

**Test Data Needed**:
- Transactions spanning 2023, 2024, 2025 (or available years)

**Edge Cases**:
- No year selected → Error message: "Pilih tahun"
- Year with no transactions → Not shown in dropdown

---

### Test 5: Export Rentang Tahun (Export Type: `year_range`)

**Purpose**: Export transactions across multiple years

**Steps**:
1. Open Export modal
2. Select "Rentang Tahun" card
3. Select "Dari Tahun"
4. Select "Sampai Tahun" (filtered to >= dari tahun)
5. Click "Export"

**Expected Result**:
- ✅ File downloads
- ✅ All transactions from year range included
- ✅ "Sampai tahun" dropdown disables years < "Dari tahun"

**Test Data Needed**:
- Transactions spanning multiple years

**Edge Cases**:
- Missing fields → Error message: "Pilih tahun dari dan sampai"
- dari > sampai → Prevented by dropdown disable
- No transactions in range → Error message

---

### Test 6: Export Per Bulan (Export Type: `month`)

**Purpose**: Export transactions from a specific month

**Steps**:
1. Open Export modal
2. Select "Per Bulan" card
3. Select "Tahun" dropdown
4. "Bulan" dropdown enables and shows only months with data
5. Select a month
6. Click "Export"

**Expected Result**:
- ✅ File downloads
- ✅ Only transactions from selected month/year included
- ✅ Month dropdown shows only months with data for selected year

**Test Data Needed**:
- Transactions in various months across years

**Edge Cases**:
- Missing tahun/bulan → Error message: "Pilih tahun dan bulan"
- Month with no data → Not shown in dropdown

---

### Test 7: Export Rentang Bulan (Export Type: `month_range`)

**Purpose**: Export transactions across multiple months

**Steps**:
1. Open Export modal
2. Select "Rentang Bulan" card
3. Select "Dari" section:
   - Select Tahun
   - Select Bulan (enables after tahun selected)
4. Select "Sampai" section:
   - Select Tahun
   - Select Bulan (enables after tahun selected)
5. Click "Export"

**Expected Result**:
- ✅ File downloads
- ✅ All transactions from month range included
- ✅ Correct date calculation (inclusive start, exclusive end)

**Test Data Needed**:
- Transactions spanning multiple months

**Edge Cases**:
- Missing any field → Error message: "Lengkapi semua field rentang bulan"
- dari > sampai (chronologically) → Should still work but may return empty
- No transactions in range → Error message

---

## 🔍 Common Issues & Solutions

### Issue 1: File doesn't download

**Symptoms**:
- Click Export button, modal closes, but no file downloads
- Browser shows "Download blocked" notification

**Solutions**:
1. Check browser popup blocker settings
2. Allow downloads from localhost
3. Check browser console for JavaScript errors
4. Verify Laravel logs: `storage/logs/laravel.log`

---

### Issue 2: File downloads but won't open

**Symptoms**:
- File downloads with .xlsx extension
- Excel shows error when opening

**Solutions**:
1. Check PHP zip extension is installed
2. Check file size is > 0 bytes
3. Try opening in Google Sheets or LibreOffice
4. Check Laravel logs for PhpSpreadsheet errors

---

### Issue 3: Error message not showing

**Symptoms**:
- Export fails but no error message appears
- Page just refreshes

**Solutions**:
1. Check error message display is added in view
2. Check session flash messages working
3. Check browser console for JavaScript errors

---

### Issue 4: Export takes too long

**Symptoms**:
- Button shows "Memproses..." but doesn't complete
- Page times out after 30 seconds

**Solutions**:
1. Increase PHP execution time:
   ```php
   // In php.ini
   max_execution_time = 300
   ```
2. Increase memory limit:
   ```php
   memory_limit = 512M
   ```
3. Consider chunking large exports (future enhancement)

---

## 📊 Testing Matrix

| Export Type | Test Status | Date Tested | Tested By | Notes |
|-------------|-------------|-------------|-----------|-------|
| all | ⬜ Not tested | - | - | - |
| range | ⬜ Not tested | - | - | - |
| dates | ⬜ Not tested | - | - | - |
| year | ⬜ Not tested | - | - | - |
| year_range | ⬜ Not tested | - | - | - |
| month | ⬜ Not tested | - | - | - |
| month_range | ⬜ Not tested | - | - | - |

**Legend**:
- ✅ Passed
- ❌ Failed
- ⬜ Not tested
- ⚠️ Partially working

---

## 🐛 Bug Report Template

If you find issues during testing, use this template:

```markdown
**Bug Title**: [Export Type] - [Brief description]

**Severity**: Critical / High / Medium / Low

**Steps to Reproduce**:
1. Step 1
2. Step 2
3. ...

**Expected Result**: What should happen

**Actual Result**: What actually happened

**Screenshots**: Attach if applicable

**Environment**:
- OS: Windows 10 / macOS / Linux
- Browser: Chrome 120.0.xxxx
- PHP Version: 8.0.x
- Laravel Version: 8.x

**Logs**: 
```
Paste relevant Laravel logs here
```

**Additional Context**: Any other relevant information
```

---

## ✅ Acceptance Criteria

All export types are considered **WORKING** when:

- [x] User can click Export button
- [x] Modal opens without JavaScript errors
- [x] User can select export type
- [x] Validation shows proper error messages
- [x] File downloads automatically
- [x] File opens in Excel without errors
- [x] File contains correct filtered data
- [x] Error messages display when export fails
- [x] Export completes within 60 seconds for reasonable dataset

---

## 📝 Post-Testing Checklist

After completing all tests:

- [ ] All 7 export types tested
- [ ] Results documented in Testing Matrix
- [ ] Bugs reported (if any)
- [ ] This document updated with findings
- [ ] User notified of testing completion

---

**Next Steps**: 
1. Perform manual testing for each export type
2. Document results
3. Fix any bugs found
4. Re-test after fixes
5. Update documentation

**Questions?** Contact system administrator or check Laravel logs at `storage/logs/laravel.log`
