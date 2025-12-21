#!/bin/bash

# Automatic Database Backup Script
# This script runs the CodeIgniter cron controller for automatic backups

# Get the directory where this script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Change to the project directory
cd "$SCRIPT_DIR"

# Run the cron controller
php index.php cron auto_backup

# Exit with the same code as the PHP script
exit $?
