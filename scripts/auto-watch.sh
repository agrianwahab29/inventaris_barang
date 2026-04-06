#!/bin/bash
# Skrip pemantau otomatis - memantau perubahan file dan melakukan commit/push otomatis
# Cara pakai: ./scripts/auto-watch.sh & (jalankan di background)

cd "$(dirname "$0")/.."

echo "👁️  Pemantauan otomatis dimulai - Memantau perubahan file..."
echo "📍 Lokasi repository: $(pwd)"
echo "🌿 Branch aktif: $(git rev-parse --abbrev-ref HEAD)"
echo ""

# Fungsi untuk melakukan commit dan push otomatis
auto_commit_push() {
    # Periksa apakah ada perubahan
    if [[ -n $(git status --porcelain) ]]; then
        echo "📦 Perubahan terdeteksi pada $(date '+%Y-%m-%d %H:%M:%S')"
        
        # Tambahkan semua perubahan
        git add .
        
        # Dapatkan daftar file yang berubah untuk pesan commit
        changed_files=$(git diff --cached --name-only | head -5 | tr '\n' ', ')
        if [[ ${#changed_files} -gt 50 ]]; then
            changed_files="${changed_files:0:50}..."
        fi
        
        # Commit dengan timestamp dan file yang berubah
        timestamp=$(date '+%Y-%m-%d %H:%M:%S')
        git commit -m "auto: backup pada $timestamp - $changed_files"
        
        # Push ke GitHub
        current_branch=$(git rev-parse --abbrev-ref HEAD)
        if git push origin "$current_branch" 2>/dev/null; then
            echo "✅ Berhasil push otomatis ke GitHub - Branch: $current_branch"
        else
            echo "❌ Gagal push - akan mencoba lagi pada perubahan berikutnya"
        fi
        echo ""
    fi
}

# Loop utama - periksa setiap 30 detik
while true; do
    auto_commit_push
    sleep 30
done
