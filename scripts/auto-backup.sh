#!/bin/bash
# Skrip backup otomatis untuk inventaris-kantor
# Cara pakai: ./scripts/auto-backup.sh

cd "$(dirname "$0")/.."

# Periksa apakah ada perubahan
if [[ -n $(git status --porcelain) ]]; then
    echo "📦 Perubahan terdeteksi, membuat backup..."
    
    # Tambahkan semua perubahan
    git add .
    
    # Commit dengan timestamp
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    git commit -m "auto-backup: $timestamp"
    
    # Push ke GitHub
    current_branch=$(git rev-parse --abbrev-ref HEAD)
    git push origin "$current_branch"
    
    echo "✅ Backup selesai dan push ke GitHub"
    echo "📍 Branch: $current_branch"
    echo "🕐 Waktu: $timestamp"
else
    echo "ℹ️ Tidak ada perubahan untuk di-backup"
fi
