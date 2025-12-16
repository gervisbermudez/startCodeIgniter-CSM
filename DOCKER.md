# Docker Setup Guide - startCodeIgniter CSM

## What's Included

This Docker setup includes:

- **PHP 7.4 Apache**: Web server with all necessary PHP extensions
- **MySQL 5.7**: Database server
- **Composer**: Dependency manager for PHP
- **Automatic Setup**: Database initialization with sample data
- **Volume Mounts**: Direct file editing without container rebuild

## System Requirements

- **Docker**: v20.10+
- **Docker Compose**: v1.29+
- **Disk Space**: ~2GB for images and database
- **RAM**: ~512MB minimum

## Quick Start

### 1. Clone or Download the Project

```bash
git clone https://github.com/gervisbermudez/startCodeIgniter-CSM.git
cd startCodeIgniter-CSM
```

### 2. Run the Installation Script

```bash
# On Linux/Mac
chmod +x install.sh
./install.sh

# Or manually start Docker
docker compose up -d
```

### 3. Access the Application

- **Frontend**: http://localhost:8081
- **Admin Panel**: http://localhost:8081/admin/login
- **Username**: `gerber`
- **Password**: `admin123`

## Configuration

### Environment Variables (.env)

Edit `.env` to customize:

```env
# Application
APP_ENV=development          # development or production
APP_BASE_URL=http://localhost:8081/

# Database
DATABASE_HOSTNAME=db        # Docker service name
DATABASE_DATABASE=start_cms_db
DATABASE_USER=ci_user
DATABASE_PASSWORD=ci_pass
```

### Docker Compose Configuration

The `docker-compose.yml` defines:

```yaml
services:
  web:
    build: .                 # Build from Dockerfile
    ports:
      - "8081:80"           # Access on port 8081
    volumes:
      - ./:/var/www/html    # Mount entire project
      
  db:
    image: mysql:5.7        # MySQL database
    ports:
      - "3306:3306"         # MySQL access
    volumes:
      - mysql_data:/var/lib/mysql  # Persistent data
```

## Common Tasks

### Access PHP Container Shell

```bash
docker exec -it ci_php56 bash
```

### View Application Logs

```bash
# PHP/Apache logs
docker logs -f ci_php56

# MySQL logs
docker logs -f ci_mysql57

# Follow logs with timestamps
docker logs -f --timestamps ci_php56
```

### Database Operations

```bash
# Access MySQL CLI
docker exec -it ci_mysql57 mysql -u ci_user -pci_pass start_cms_db

# Backup database
docker exec ci_mysql57 mysqldump -u ci_user -pci_pass start_cms_db > backup.sql

# Restore database
docker exec -i ci_mysql57 mysql -u ci_user -pci_pass start_cms_db < backup.sql

# Export to CSV
docker exec ci_mysql57 mysql -u ci_user -pci_pass start_cms_db -e "SELECT * FROM users;" > export.csv
```

### Composer Operations

```bash
# Install dependencies
docker exec ci_php56 composer install

# Update dependencies
docker exec ci_php56 composer update

# Autoload optimization
docker exec ci_php56 composer dump-autoload
```

### Generate Password Hashes

```bash
# Generate bcrypt hash for password
docker exec ci_php56 php -r "echo password_hash('mypassword', PASSWORD_BCRYPT);"
```

## Troubleshooting

### Containers Won't Start

```bash
# Check if ports are already in use
netstat -an | grep 8081
netstat -an | grep 3306

# Remove stopped containers
docker compose down

# Rebuild everything
docker compose down -v
docker compose up --build -d
```

### Permission Denied Errors

The Docker setup automatically sets permissions on startup. If you still get permission errors:

```bash
# Restart containers
docker compose restart

# Or rebuild
docker compose down -v && docker compose up --build -d
```

### Database Connection Errors

```bash
# Check if MySQL is running
docker ps | grep mysql

# View MySQL logs
docker logs ci_mysql57

# Test connection from PHP container
docker exec ci_php56 php -r "
\$mysqli = new mysqli('db', 'ci_user', 'ci_pass', 'start_cms_db');
echo \$mysqli->connect_error ? 'Error: ' . \$mysqli->connect_error : 'Connected!';
"
```

### Reset Everything

```bash
# Stop all containers
docker compose down

# Remove all volumes (WARNING: deletes database!)
docker compose down -v

# Rebuild from scratch
docker compose up --build -d
```

### Change Admin Password

```bash
# Generate new password hash
HASH=$(docker exec ci_php56 php -r "echo password_hash('newpassword', PASSWORD_BCRYPT);")

# Update in database
docker exec ci_mysql57 mysql -u ci_user -pci_pass start_cms_db -e "UPDATE user SET password='$HASH' WHERE username='gerber';"
```

## Performance Tips

### Optimize Database

```bash
# Access MySQL
docker exec -it ci_mysql57 mysql -u ci_user -pci_pass start_cms_db

# Check indexes
ANALYZE TABLE users;
OPTIMIZE TABLE users;
```

### Clear Caches

```bash
# Clear application cache
docker exec ci_php56 rm -rf /var/www/html/application/cache/*

# Clear theme cache
docker exec ci_php56 rm -rf /var/www/html/themes/*/cache/*
```

### Monitor Resources

```bash
# CPU and memory usage
docker stats

# Container disk usage
docker system df
```

## Security Considerations

1. **Change Default Password**: Always change the default `admin123` password
2. **Update .env**: Change database passwords for production
3. **Enable HTTPS**: Use a reverse proxy (nginx) for SSL in production
4. **Backup Data**: Regular backups of the database
5. **Update Images**: Keep Docker images updated

## Production Deployment

For production use:

1. Use `.env.production` with secure credentials
2. Set `APP_ENV=production` in .env
3. Use environment-specific docker-compose files
4. Implement proper logging and monitoring
5. Set up automatic backups
6. Use a reverse proxy (nginx) for SSL/TLS
7. Implement rate limiting and security headers

## Network Configuration

### Access from Other Machines

To access the application from another machine on the network:

1. Find your machine's IP address:
   ```bash
   # Linux/Mac
   ifconfig | grep "inet "
   
   # Windows
   ipconfig
   ```

2. Update `.env`:
   ```env
   APP_BASE_URL=http://YOUR_IP:8081/
   ```

3. Access from another machine:
   ```
   http://YOUR_IP:8081
   ```

## Docker Cleanup

```bash
# Remove unused images
docker image prune

# Remove unused volumes
docker volume prune

# Remove unused networks
docker network prune

# Full cleanup (WARNING: removes all unused data)
docker system prune -a
```

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [CodeIgniter Documentation](https://codeigniter.com/user_guide/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## Support

For issues or questions:
- Check the logs: `docker logs ci_php56`
- Review this guide
- Open an issue on GitHub
