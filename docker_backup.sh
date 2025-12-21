#!/bin/bash

# Docker Backup Helper Script
# Facilita la ejecución de backups en el contenedor Docker

CONTAINER_NAME="ci_php56"
PROJECT_PATH="/var/www/html"

echo "🐳 Docker Backup Helper"
echo "======================="
echo ""

# Check if container is running
if ! docker ps | grep -q $CONTAINER_NAME; then
    echo "❌ Error: Container '$CONTAINER_NAME' is not running"
    echo "Start it with: docker-compose up -d"
    exit 1
fi

echo "✓ Container '$CONTAINER_NAME' is running"
echo ""

# Show menu
echo "Select an option:"
echo "1) Test cron controller"
echo "2) Run automatic backup (manual trigger)"
echo "3) View backup files"
echo "4) Enable automatic backups"
echo "5) Disable automatic backups"
echo "6) Show backup configuration"
echo "7) Clean old backups (keep last 5)"
echo "0) Exit"
echo ""
read -p "Option: " option

case $option in
    1)
        echo ""
        echo "Testing cron controller..."
        docker exec $CONTAINER_NAME php $PROJECT_PATH/index.php cron test
        ;;
    2)
        echo ""
        echo "Running automatic backup..."
        docker exec $CONTAINER_NAME php $PROJECT_PATH/index.php cron auto_backup
        ;;
    3)
        echo ""
        echo "Backup files in container:"
        docker exec $CONTAINER_NAME ls -lh $PROJECT_PATH/backups/database/
        ;;
    4)
        echo ""
        echo "Enabling automatic backups..."
        docker exec $CONTAINER_NAME php -r "
        require '$PROJECT_PATH/index.php';
        \$CI =& get_instance();
        \$CI->db->where('config_name', 'AUTO_BACKUP_ENABLED');
        \$CI->db->update('site_config', ['config_value' => 'Si']);
        echo '✓ Automatic backups enabled\n';
        "
        ;;
    5)
        echo ""
        echo "Disabling automatic backups..."
        docker exec $CONTAINER_NAME php -r "
        require '$PROJECT_PATH/index.php';
        \$CI =& get_instance();
        \$CI->db->where('config_name', 'AUTO_BACKUP_ENABLED');
        \$CI->db->update('site_config', ['config_value' => 'No']);
        echo '✓ Automatic backups disabled\n';
        "
        ;;
    6)
        echo ""
        echo "Backup configuration:"
        docker exec $CONTAINER_NAME php -r "
        require '$PROJECT_PATH/index.php';
        \$CI =& get_instance();
        \$configs = \$CI->db->where_in('config_name', [
            'AUTO_BACKUP_ENABLED',
            'AUTO_BACKUP_FREQUENCY',
            'AUTO_BACKUP_RETENTION',
            'AUTO_BACKUP_TIME',
            'LAST_AUTO_BACKUP'
        ])->get('site_config')->result();
        foreach (\$configs as \$c) {
            printf(\"%-25s: %s\n\", \$c->config_name, \$c->config_value);
        }
        "
        ;;
    7)
        echo ""
        echo "Cleaning old backups (keeping last 5)..."
        docker exec $CONTAINER_NAME bash -c "
        cd $PROJECT_PATH/backups/database/
        ls -t auto_*.gz | tail -n +6 | xargs -r rm -v
        echo 'Done!'
        "
        ;;
    0)
        echo "Goodbye!"
        exit 0
        ;;
    *)
        echo "Invalid option"
        exit 1
        ;;
esac

echo ""
echo "Done!"
