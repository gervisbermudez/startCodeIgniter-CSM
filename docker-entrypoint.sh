#!/bin/bash
set -e

# Crear directorios necesarios si no existen
mkdir -p /var/www/html/application/cache
mkdir -p /var/www/html/application/logs
mkdir -p /var/www/html/themes/iPortfolio/cache
mkdir -p /var/www/html/themes/awesomeTheme/cache
mkdir -p /var/www/html/themes/myGreatTheme/cache
mkdir -p /var/www/html/themes/nicoTheme/cache
mkdir -p /var/www/html/themes/SinglePageTheme-1.1.0/cache
mkdir -p /var/www/html/uploads

# Establecer permisos
chmod -R 777 /var/www/html/application/cache
chmod -R 777 /var/www/html/application/logs
chmod -R 777 /var/www/html/themes
chmod -R 777 /var/www/html/uploads

# Cambiar propietario a www-data (usuario de Apache)
chown -R www-data:www-data /var/www/html/application/cache
chown -R www-data:www-data /var/www/html/application/logs
chown -R www-data:www-data /var/www/html/themes
chown -R www-data:www-data /var/www/html/uploads

# Ejecutar Apache
exec apache2-foreground
