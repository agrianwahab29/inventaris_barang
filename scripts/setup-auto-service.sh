#!/bin/bash
# Setup Auto-Watch as Systemd Service (Linux)
# Run with sudo

set -e

REPO_PATH="/path/to/inventaris-kantor"
SCRIPT_PATH="$REPO_PATH/scripts/auto-watch.sh"
SERVICE_NAME="git-autowatch"

echo "=========================================="
echo "Setup Auto-Watch Git Backup Service"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root (use sudo)"
    exit 1
fi

# Create systemd service file
cat > /etc/systemd/system/$SERVICE_NAME.service << EOF
[Unit]
Description=Git Auto-Watch Backup Service
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

# Enable and start service
systemctl enable $SERVICE_NAME
systemctl start $SERVICE_NAME

echo "✅ Service installed and started!"
echo ""
echo "Commands:"
echo "   sudo systemctl status $SERVICE_NAME  - Check status"
echo "   sudo systemctl stop $SERVICE_NAME    - Stop service"
echo "   sudo systemctl start $SERVICE_NAME   - Start service"
echo "   sudo systemctl restart $SERVICE_NAME - Restart service"
echo ""
echo "Logs:"
echo "   sudo journalctl -u $SERVICE_NAME -f"
