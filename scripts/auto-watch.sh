#!/bin/bash
# Auto-watch script - monitors file changes and auto-commits/pushes
# Usage: ./scripts/auto-watch.sh & (run in background)

cd "$(dirname "$0")/.."

echo "👁️  Auto-watch started - Monitoring for changes..."
echo "📍 Repository: $(pwd)"
echo "🌿 Branch: $(git rev-parse --abbrev-ref HEAD)"
echo ""

# Function to auto-commit and push
auto_commit_push() {
    # Check if there are changes
    if [[ -n $(git status --porcelain) ]]; then
        echo "📦 Changes detected at $(date '+%Y-%m-%d %H:%M:%S')"
        
        # Add all changes
        git add .
        
        # Get list of changed files for commit message
        changed_files=$(git diff --cached --name-only | head -5 | tr '\n' ', ')
        if [[ ${#changed_files} -gt 50 ]]; then
            changed_files="${changed_files:0:50}..."
        fi
        
        # Commit with timestamp and changed files
        timestamp=$(date '+%Y-%m-%d %H:%M:%S')
        git commit -m "auto: backup at $timestamp - $changed_files"
        
        # Push to GitHub
        current_branch=$(git rev-parse --abbrev-ref HEAD)
        if git push origin "$current_branch" 2>/dev/null; then
            echo "✅ Auto-pushed to GitHub - Branch: $current_branch"
        else
            echo "❌ Push failed - will retry on next change"
        fi
        echo ""
    fi
}

# Main loop - check every 30 seconds
while true; do
    auto_commit_push
    sleep 30
done
