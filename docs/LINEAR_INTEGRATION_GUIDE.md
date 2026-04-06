# Linear Integration Guide

Panduan lengkap untuk mengintegrasikan project ini dengan Linear.app

---

## 🎯 Masalah MCP Linear

MCP Linear error biasanya disebabkan oleh:
1. **Team ID tidak di-set** - Linear butuh team ID untuk membuat issues
2. **API Key tidak valid** - Token expired atau salah
3. **Format parameter salah** - MCP Linear membutuhkan format tertentu

---

## ✅ Solusi: Gunakan Linear API Langsung

Kami telah menyediakan 3 cara untuk membuat issues di Linear:

### 📁 Files yang Dibuat:

| File | Deskripsi | Platform |
|------|-----------|----------|
| `linear-issues.json` | Data issues dalam format JSON | All |
| `linear_import.py` | Python script untuk import | All (dengan Python) |
| `linear-helper.sh` | Bash script untuk Linux/Mac | Linux/Mac |
| `linear-helper.bat` | Batch script untuk Windows | Windows |

---

## 🚀 Cara Penggunaan (Recommended: Python Script)

### Step 1: Setup Environment Variables

**Windows (PowerShell):**
```powershell
$env:LINEAR_API_KEY = "lin_api_xxxxxxxxxxxx"
$env:LINEAR_TEAM_ID = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
```

**Windows (CMD):**
```cmd
set LINEAR_API_KEY=lin_api_xxxxxxxxxxxx
set LINEAR_TEAM_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

**Linux/Mac:**
```bash
export LINEAR_API_KEY="lin_api_xxxxxxxxxxxx"
export LINEAR_TEAM_ID="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
```

### Step 2: Dapatkan API Key dan Team ID

1. **API Key:**
   - Buka https://linear.app
   - Login → Settings → API → Personal API keys
   - Click "Create API key"
   - Copy key (format: `lin_api_...`)

2. **Team ID:**
   - Run: `python scripts/linear_import.py --teams`
   - Copy ID dari team yang ingin digunakan

### Step 3: Import Issues

**Import semua issues:**
```bash
cd scripts
python linear_import.py --import
```

**Import satu issue saja (index 0 = issue pertama):**
```bash
python linear_import.py --issue 0
```

**List teams:**
```bash
python linear_import.py --teams
```

---

## 🪟 Alternatif: Windows Batch Script

Jika tidak punya Python, gunakan batch script:

### Step 1: Edit File

Buka `scripts/linear-helper.bat` dan ganti:
```batch
set LINEAR_API_KEY=YOUR_LINEAR_API_KEY
set TEAM_ID=YOUR_TEAM_ID
```

### Step 2: Jalankan

```batch
cd scripts
linear-helper.bat teams        # List teams
linear-helper.bat create "Title" "Description"   # Create issue
```

---

## 🐧 Alternatif: Linux/Mac Bash Script

```bash
# Edit file terlebih dahulu
nano scripts/linear-helper.sh

# Ganti YOUR_LINEAR_API_KEY dan YOUR_TEAM_ID

# Jadikan executable
chmod +x scripts/linear-helper.sh

# Jalankan
./scripts/linear-helper.sh teams
./scripts/linear-helper.sh create "Title" "Description"
```

---

## 📋 Issues yang Akan Dibuat

Setelah import, Anda akan memiliki 6 issues di Linear:

| # | Issue | Priority | Estimate |
|---|-------|----------|----------|
| 1 | Security Audit | 🔴 High | 4 jam |
| 2 | Code Architecture Review | 🔴 High | 5 jam |
| 3 | Database Schema Review | 🔴 High | 4 jam |
| 4 | Performance Assessment | 🟡 Medium | 4 jam |
| 5 | Technical Debt Assessment | 🟡 Medium | 3 jam |
| 6 | Feature Gap Analysis | 🟡 Medium | 3 jam |

**Total:** 23 jam estimasi

---

## 🔧 Troubleshooting

### Error: "Invalid API key"

**Solusi:**
- Pastikan API key benar (format: `lin_api_...`)
- Cek apakah key masih aktif di Linear Settings

### Error: "Team not found"

**Solusi:**
- Run `python linear_import.py --teams` untuk melihat team ID yang valid
- Pastikan Anda memiliki akses ke team tersebut

### Error: "Permission denied"

**Solusi:**
- Pastikan API key memiliki permission "Write"
- Cek di Linear Settings → API → Permissions

### Error: "Module not found" (Python)

**Solusi:**
```bash
pip install requests
```

---

## 🎨 Struktur Project di Linear (Recommended)

Setelah import, organize issues di Linear seperti ini:

```
📦 Epic: Analisis Sistem Inventaris Kantor
├── 🔴 High Priority
│   ├── [ANALYSIS-001] Security Audit
│   ├── [ANALYSIS-002] Code Architecture Review  
│   └── [ANALYSIS-003] Database Schema Review
└── 🟡 Medium Priority
    ├── [ANALYSIS-004] Performance Assessment
    ├── [ANALYSIS-005] Technical Debt Assessment
    └── [ANALYSIS-006] Feature Gap Analysis
```

### Labels yang Direkomendasikan:

Buat labels di Linear:
- `analysis` - Untuk semua analysis issues
- `security` - Security-related
- `architecture` - Code architecture
- `database` - Database/schema
- `performance` - Performance optimization
- `technical-debt` - Refactoring needs
- `features` - Feature analysis

---

## 📝 Manual Copy-Paste (Fallback)

Jika semua script gagal, Anda bisa copy-paste manual:

1. Buka file `docs/LINEAR_ANALYSIS_PROJECT.md`
2. Copy setiap issue template
3. Paste ke Linear App satu per satu

---

## 🔗 Referensi

- **Linear API Docs:** https://developers.linear.com/docs/graphql/working-with-the-graphql-api
- **Linear App:** https://linear.app
- **Project Docs:** `docs/LINEAR_ANALYSIS_PROJECT.md`

---

## ⚡ Quick Start Checklist

- [ ] Dapatkan Linear API Key dari Settings → API
- [ ] Set environment variables (LINEAR_API_KEY, LINEAR_TEAM_ID)
- [ ] Install Python (jika belum ada)
- [ ] Install requests: `pip install requests`
- [ ] Run: `python scripts/linear_import.py --teams`
- [ ] Copy Team ID
- [ ] Run: `python scripts/linear_import.py --import`
- [ ] Cek Linear App untuk memastikan issues terbuat

---

**Butuh bantuan?** Cek file `docs/LINEAR_ANALYSIS_PROJECT.md` untuk detail lengkap.
