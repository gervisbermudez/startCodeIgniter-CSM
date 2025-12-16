FROM php:7.4-apache

# Instalar curl, git y libzip-dev (necesarios para composer y zip)
RUN apt-get update && apt-get install -y curl git libzip-dev && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilitar mod_rewrite y mod_headers (esto soluciona el 500)
RUN a2enmod rewrite headers

# INSTALAR EXTENSIONES PHP (Esto es lo que podría faltar ahora)
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Copiar archivos del proyecto
WORKDIR /var/www/html
COPY . .

# Instalar dependencias de composer
RUN composer install --no-interaction --prefer-dist

# Copiar script de entrada
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]