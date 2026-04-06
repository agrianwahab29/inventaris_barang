#!/bin/bash
# Setup Auto-Watch sebagai Systemd Service (Linux)
# Jalankan dengan sudo

set -e

REPO_PATH="/path/to/inventaris-kantor"
SCRIPT_PATH="$REPO_PATH/scripts/auto-watch.sh"
SERVICE_NAME="git-autowatch"

echo "=========================================="
echo "Setup Layanan Auto-Watch Git Backup"
echo "=========================================="
echo ""

# Periksa apakah dijalankan sebagai root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Harap jalankan sebagai root (gunakan sudo)"
    exit 1
fi

# Buat file systemd service
cat > /etc/systemd/system/$SERVICE_NAME.service << EOF
[Unit]
Description=Layanan Auto-Watch Git Backup
After=network.target

[Service]
Type=simple
User=$SUDO_USER
WorkingDirectory=$REPO_PATH
ExecStart=$SCRIPT_PATH
Restart=always
RestartSec=30

[Install]
WantedBy=multi-user.target
EOF

# Reload systemd
systemctl daemon-reload

# Enable dan start service
systemctl enable $SERVICE_NAME
systemctl start $SERVICE_NAME

echo "✅ Service berhasil diinstal dan dijalankan!"
echo ""
echo "Perintah untuk mengontrol:"
echo "   sudo systemctl status $SERVICE_NAME  - Cek status"
echo "   sudo systemctl stop $SERVICE_NAME    - Hentikan"
echo "   sudo systemctl start $SERVICE_NAME   - Jalankan"
echo "   sudo systemctl restart $SERVICE_NAME - Restart"
echo ""
echo "Log:"
echo "   sudo journalctl -u $SERVICE_NAME -f"
