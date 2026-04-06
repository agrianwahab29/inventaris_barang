# AGENTS.md - Sistem Inventaris Kantor

## Project Snapshot

**Repository**: https://github.com/agrianwahab29/inventaris_barang.git  
**Tech Stack**: Laravel 8.x, PHP 8.0+, MySQL, Bootstrap 5, JavaScript  
**Type**: Single Laravel Application (Monolithic)  
**Purpose**: Sistem manajemen inventaris barang kantor dengan tracking transaksi dan stok opname

---

## Auto-Push GitHub Setup

### 1. Git Hooks Auto-Commit (Opsional)

Buat file `.git/hooks/post-commit` untuk auto-push setelah commit:

```bash
#!/bin/bash
# Auto-push ke GitHub setelah commit
branch=$(git rev-parse --abbrev-ref HEAD)
if [ "$branch" = "master" ] || [ "$branch" = "main" ]; then
    echo "🚀 Auto-pushing to GitHub..."
    git push origin "$branch"
fi
```

Jadikan executable:
```bash
chmod +x .git/hooks/post-commit
```

### 2. Auto-Backup Script

Buat file `scripts/auto-backup.sh`:

```bash
#!/bin/bash
# Auto-backup script untuk inventaris-kantor

cd "$(dirname "$0")/.."

# Check apakah ada perubahan
if [[ -n $(git status -s) ]]; then
    echo "📦 Changes detected, creating backup..."
    
    # Add semua perubahan
    git add .
    
    # Commit dengan timestamp
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    git commit -m "auto-backup: $timestamp"
    
    # Push ke GitHub
    git push origin $(git rev-parse --abbrev-ref HEAD)
    
    echo "✅ Backup completed and pushed to GitHub"
else
    echo "ℹ️ No changes to backup"
fi
```

Jadikan executable:
```bash
chmod +x scripts/auto-backup.sh
```

### 3. Windows Auto-Backup (Batch)

Buat file `scripts/auto-backup.bat`:

```batch
@echo off
cd /d "%~dp0\.."

REM Check apakah ada perubahan
for /f "tokens=*" %%a in ('git status --porcelain') do (
    echo Changes detected, creating backup...
    
    REM Add semua perubahan
    git add .
    
    REM Commit dengan timestamp
    for /f "tokens=2-4 delims=/ " %%b in ('date /t') do (
        for /f "tokens=1-2 delims=: " %%e in ('time /t') do (
            git commit -m "auto-backup: %%c-%%b-%%d %%e:%%f"
        )
    )
    
    REM Push ke GitHub
    git push origin master
    
    echo Backup completed and pushed to GitHub
    goto :eof
)

echo No changes to backup
```

---

## 🚀 FULLY AUTOMATIC BACKUP (Tanpa Perintah Manual)

### Opsi 1: Auto-Watch (Real-time Monitoring)

**Cara kerja:** Script berjalan di background, memantau perubahan file setiap 30 detik, otomatis commit & push.

#### Windows:
```bash
# Jalankan dan biarkan window tetap terbuka
scripts\auto-watch.bat
```

#### Linux/Mac:
```bash
# Jalankan di background
./scripts/auto-watch.sh &
```

**Fitur:**
- ✅ Monitoring otomatis setiap 30 detik
- ✅ Auto-commit dengan timestamp
- ✅ Auto-push ke GitHub
- ✅ Tidak perlu ingat-ingat backup

---

### Opsi 2: Setup Windows Service (Recommended)

**Cara kerja:** Install sebagai Windows Service, berjalan otomatis bahkan saat laptop restart.

#### Langkah-langkah:

1. **Run as Administrator:**
   - Klik kanan `scripts/setup-auto-service.bat`
   - Pilih "Run as Administrator"

2. **Service akan berjalan otomatis:**
   - Setiap 5 menit memeriksa perubahan
   - Auto-commit & push jika ada perubahan
   - Berjalan di background (tidak perlu buka window)

3. **Command untuk mengontrol:**
   ```bash
   # Start service
   schtasks /run /tn "GitAutoWatch"
   
   # Stop service
   schtasks /end /tn "GitAutoWatch"
   
   # Hapus service
   schtasks /delete /tn "GitAutoWatch" /f
   ```

---

### Opsi 3: Setup Linux Systemd Service

**Cara kerja:** Install sebagai systemd service, berjalan otomatis 24/7.

#### Langkah-langkah:

```bash
# Jalankan setup script dengan sudo
sudo ./scripts/setup-auto-service.sh
```

**Command untuk mengontrol:**
```bash
# Cek status
sudo systemctl status git-autowatch

# Stop service
sudo systemctl stop git-autowatch

# Start service
sudo systemctl start git-autowatch

# Lihat log
sudo journalctl -u git-autowatch -f
```

---

### Opsi 4: Git Hook (Auto-push setelah commit)

**Cara kerja:** Setiap kali Anda commit, otomatis push ke GitHub.

#### Setup:

**Windows:**
```bash
# Buat file post-commit
echo @echo off > .git\hooks\post-commit
echo git push origin master >> .git\hooks\post-commit
```

**Linux/Mac:**
```bash
# Buat file post-commit
cat > .git/hooks/post-commit << 'EOF'
#!/bin/bash
git push origin $(git rev-parse --abbrev-ref HEAD)
EOF

# Jadikan executable
chmod +x .git/hooks/post-commit
```

**Cara pakai:**
```bash
# Anda hanya perlu commit, push otomatis
git add .
git commit -m "update fitur"
# Push otomatis terjadi!
```

---

## Workflow Standar

### Setiap Kali Coding

1. **Pull terlebih dahulu** (untuk sinkronisasi):
   ```bash
   git pull origin master
   ```

2. **Lakukan perubahan** (edit, tambah, hapus file)

3. **Commit dan Push**:
   ```bash
   git add .
   git commit -m "deskripsi perubahan"
   git push origin master
   ```

### Atau Gunakan Auto-Backup

```bash
# Jalankan script backup
./scripts/auto-backup.sh        # Linux/Mac
scripts\auto-backup.bat        # Windows
```

---

## Recovery & Rollback

### Jika Terjadi Kesalahan

**1. Lihat history commit:**
```bash
git log --oneline -10
```

**2. Rollback ke commit sebelumnya:**
```bash
# Soft rollback (keep changes)
git reset --soft HEAD~1

# Hard rollback (discard changes)
git reset --hard HEAD~1

# Rollback ke commit tertentu
git reset --hard <commit-hash>
```

**3. Ambil file spesifik dari commit lama:**
```bash
# Ambil satu file dari commit tertentu
git checkout <commit-hash> -- path/to/file.php

# Contoh: Ambil TransaksiController dari 2 commit lalu
git checkout HEAD~2 -- app/Http/Controllers/TransaksiController.php
```

**4. Lihat perubahan di GitHub:**
- Buka: https://github.com/agrianwahab29/inventaris_barang/commits/master
- Klik commit untuk lihat perubahan
- Klik "Browse files" untuk lihat state kode di commit tersebut

---

## Branch Strategy

### Struktur Branch

```
master (main)     ← Production ready, stable
  │
  ├── develop     ← Development branch (opsional)
  │
  ├── feature/nama-fitur    ← Fitur baru
  ├── bugfix/nama-bug       ← Perbaikan bug
  └── hotfix/nama-hotfix    ← Perbaikan urgent
```

### Panduan Branch

**Untuk project ini (single developer):**
- Gunakan `master` untuk semua development
- Tidak perlu branch tambahan untuk project kecil

**Jika butuh isolasi:**
```bash
# Buat branch baru
git checkout -b feature/nama-fitur

# Push branch baru
git push -u origin feature/nama-fitur

# Merge ke master
git checkout master
git merge feature/nama-fitur
git push origin master

# Hapus branch lokal
git branch -d feature/nama-fitur
```

---

## Commit Message Format

### Format Standar

```
type(scope): deskripsi singkat

[body - opsional, penjelasan detail]
```

### Tipe Commit

- `feat`: Fitur baru
- `fix`: Perbaikan bug
- `docs`: Dokumentasi
- `style`: Formatting (tidak mengubah logic)
- `refactor`: Restrukturasi kode
- `test`: Testing
- `chore`: Maintenance

### Contoh

```bash
git commit -m "feat(transaksi): add export to Excel functionality"
git commit -m "fix(barang): correct stock calculation bug"
git commit -m "docs: update README with installation steps"
git commit -m "refactor: simplify DashboardController queries"
```

---

## File Structure

```
inventaris-kantor/
├── app/
│   ├── Console/Commands/      ← Artisan commands
│   ├── Exports/               ← Excel export classes
│   ├── Http/
│   │   ├── Controllers/       ← Controllers
│   │   └── Middleware/        ← Middleware
│   └── Models/                ← Eloquent models
├── config/                    ← Configuration files
├── database/
│   ├── migrations/            ← Database migrations
│   └── seeders/               ← Database seeders
├── docs/                      ← Documentation
├── dummy/                     ← Dummy data files
├── public/                    ← Public assets
├── resources/
│   ├── views/                 ← Blade templates
│   ├── js/                    ← JavaScript files
│   └── css/                   ← CSS files
├── routes/
│   └── web.php                ← Web routes
├── scripts/                   ← Custom scripts
├── storage/                   ← Storage (logs, cache, uploads)
├── tests/                     ← Unit & Feature tests
├── .env.example               ← Environment template
├── composer.json              ← PHP dependencies
└── README.md                  ← Project documentation
```

---

## Key Files & Patterns

### Controllers (app/Http/Controllers/)

**Pattern:**
- Gunakan `BarangController` untuk resource Barang
- Gunakan `TransaksiController` untuk transaksi
- Gunakan `DashboardController` untuk dashboard

**Contoh:**
```php
// app/Http/Controllers/BarangController.php
public function index()
{
    $barangs = Barang::with('ruangan')->paginate(10);
    return view('barang.index', compact('barangs'));
}
```

### Models (app/Models/)

**Pattern:**
- Gunakan Eloquent relationships
- Definisikan fillable fields
- Gunakan scopes untuk query reusable

**Contoh:**
```php
// app/Models/Barang.php
class Barang extends Model
{
    protected $fillable = ['kode', 'nama', 'satuan', 'stok', 'ruangan_id'];
    
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
```

### Views (resources/views/)

**Pattern:**
- Gunakan Blade templating
- Extend layouts/app.blade.php
- Gunakan partials untuk komponen reusable

**Contoh:**
```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Barang</h1>
        @include('barang._table', ['barangs' => $barangs])
    </div>
@endsection
```

---

## Commands Reference

### Development

```bash
# Install dependencies
composer install
npm install

# Run development server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Git Operations

```bash
# Status
git status

# Add files
git add .
git add nama-file.php

# Commit
git commit -m "pesan commit"

# Push
git push origin master

# Pull
git pull origin master

# View log
git log --oneline -10

# View diff
git diff

# Stash changes
git stash
git stash pop
```

### Export/Import

```bash
# Export data transaksi
php artisan export:transaksi

# Import CSV
php artisan import:csv-transaksi

# Update stok
php artisan update:stok-barang
```

---

## Security & Best Practices

### Jangan Commit

- ❌ `.env` (sudah di .gitignore)
- ❌ `vendor/` (sudah di .gitignore)
- ❌ `node_modules/` (sudah di .gitignore)
- ❌ File database (`.sql`, `.sqlite`)
- ❌ File upload user (kecuali dummy)
- ❌ API keys, passwords, tokens

### Selalu Commit

- ✅ Migration files
- ✅ Seeder files
- ✅ Configuration files (config/)
- ✅ Documentation (docs/)
- ✅ Scripts (scripts/)

### Backup Rutin

**Manual:**
```bash
./scripts/auto-backup.sh
```

**Atau setup cron job (Linux/Mac):**
```bash
# Edit crontab
crontab -e

# Tambahkan (backup setiap jam)
0 * * * * cd /path/to/inventaris-kantor && ./scripts/auto-backup.sh
```

---

## Troubleshooting

### Masalah Push

**Error: "rejected non-fast-forward"**
```bash
git pull origin master --rebase
git push origin master
```

**Error: "Permission denied"**
- Cek remote URL: `git remote -v`
- Pastikan punya akses ke repository

### Masalah Merge

**Conflict saat pull:**
```bash
# Lihat file yang conflict
git status

# Edit file conflict, lalu
git add .
git commit -m "resolve merge conflict"
```

### Recovery File

**File terhapus tapi sudah di-commit:**
```bash
# Lihat history file
git log --follow -- nama-file.php

# Restore file
git checkout <commit-hash> -- nama-file.php
```

---

## Definition of Done

Sebelum push ke GitHub, pastikan:

- [ ] Kode berjalan tanpa error
- [ ] Tidak ada file sensitif (.env, credentials)
- [ ] Commit message jelas
- [ ] Sudah di-test (jika ada fitur baru)
- [ ] Documentation updated (jika perlu)

---

## Quick Commands Cheat Sheet

```bash
# Setup baru
git clone https://github.com/agrianwahab29/inventaris_barang.git
cd inventaris_barang
cp .env.example .env
composer install
php artisan key:generate

# Daily workflow
git pull origin master
# ... edit files ...
git add .
git commit -m "feat: deskripsi"
git push origin master

# Emergency rollback
git log --oneline -5
git reset --hard <commit-hash>
git push origin master --force  # Hati-hati!

# Backup otomatis
./scripts/auto-backup.sh
```

---

## Links & Resources

- **Repository**: https://github.com/agrianwahab29/inventaris_barang
- **Commits History**: https://github.com/agrianwahab29/inventaris_barang/commits/master
- **Issues**: https://github.com/agrianwahab29/inventaris_barang/issues
- **Laravel Docs**: https://laravel.com/docs/8.x

---

## Catatan Penting

> **Auto-push sudah aktif!** Setiap kali Anda commit, kode otomatis tersimpan di GitHub. Jika terjadi kesalahan, Anda selalu bisa mengambil kode dari commit sebelumnya melalui GitHub atau menggunakan `git checkout`.

> **Selalu pull sebelum push** untuk menghindari conflict.

> **Gunakan commit message yang jelas** agar mudah tracking perubahan.
