#!/bin/bash
# Auto-backup script untuk inventaris-kantor
# Usage: ./scripts/auto-backup.sh

cd "$(dirname "$0")/.."

# Check apakah ada perubahan
if [[ -n $(git status --porcelain) ]]; then
    echo "📦 Changes detected, creating backup..."
    
    # Add semua perubahan
    git add .
    
    # Commit dengan timestamp
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    git commit -m "auto-backup: $timestamp"
    
    # Push ke GitHub
    current_branch=$(git rev-parse --abbrev-ref HEAD)
    git push origin "$current_branch"
    
    echo "✅ Backup completed and pushed to GitHub"
    echo "📍 Branch: $current_branch"
    echo "🕐 Time: $timestamp"
else
    echo "ℹ️ No changes to backup"
fi
