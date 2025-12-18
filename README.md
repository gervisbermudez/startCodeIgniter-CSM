![startCodeigneiter CSM](https://repository-images.githubusercontent.com/233129678/7ad83200-f12e-11ea-8538-ab49ede15585)

# startCodeigneiter CSM

startCodeIgniter CSM is a Lightweight Content Management System based on CodeIgniter Framework: cloud-enabled, mobile-ready, offline-storage and HTML5 editor.

## Features

- Create custom pages and blogs
- Manage your files and folders
- Manage events, videos and photos
- Create users with different access levels
- Create categories and subcategories
- Dynamic form contents
- Dashboard with widgets
- People section to manage users and groups
- Received forms to view and manage form submissions
- Fragment section to create and manage reusable content
- Albums section to manage photo albums
- Videos section to manage video content
- Models section to manage custom content types
- REST API to consume the data
- Import and export data easily
- Theme and website configuration management

## Quick Start with Docker

### Prerequisites

- Docker and Docker Compose installed on your system
- Git (optional, for cloning the repository)

### Installation Steps

1. **Clone the repository** (or download the project)
```bash
git clone https://github.com/gervisbermudez/startCodeIgniter-CSM.git
cd startCodeIgniter-CSM
```

2. **Start the application with Docker**
```bash
docker compose up -d
```

This command will:
- Download and build the PHP 7.4 Apache image
- Install all Composer dependencies
- Create and configure the MySQL database
- Import the initial database schema
- Set up proper file permissions

3. **Access the application**
- **Frontend**: http://localhost:8081
- **Admin Panel**: http://localhost:8081/admin/login

### Default Admin Credentials

After installation, you can login with:
- **Username**: `gerber`
- **Password**: `admin123`

### Database Access (Optional)

If you need to manage the database directly:

**Connection Details**:
- **Host**: `localhost`
- **Port**: `3306`
- **Username**: `ci_user`
- **Password**: `ci_pass`
- **Database**: `start_cms_db`

You can use tools like DBeaver or MySQL Workbench to connect.

### Useful Docker Commands

```bash
# Stop the application
docker compose down

# View logs
docker logs ci_php56        # PHP/Apache logs
docker logs ci_mysql57      # MySQL logs

# Access the database via terminal
mysql -h localhost -u ci_user -pci_pass start_cms_db

# Restart containers
docker compose restart
```

### Project Structure

```
startCodeIgniter-CSM/
├── application/          # CodeIgniter application files
│   ├── config/          # Configuration files
│   ├── controllers/      # Application controllers
│   ├── models/          # Database models
│   ├── views/           # View templates
│   ├── database/        # Database schema and dumps
│   └── core/            # Core classes
├── public/              # Static assets (CSS, JS, images)
├── themes/              # Theme templates
├── uploads/             # User uploaded files
├── vendor/              # Composer dependencies
├── Dockerfile           # Docker configuration
├── docker-compose.yml   # Docker Compose configuration
└── .env                 # Environment variables
```

## Available Scripts

The project includes several utility scripts to help with development:

### NPM Scripts

```bash
# Build CSS from SCSS (production)
npm run build

# Development server with hot reload (if configured)
npm run dev
```

### Shell Scripts

```bash
# Validate development environment
./bin/validate-env.sh

# Backup database
./bin/backup-db.sh

# Start development server
./bin/server.sh

# Check git diff status
./bin/check-diff.sh
```

### Environment Configuration

The `.env` file contains important configuration:

```
APP_ENV=development
APP_BASE_URL=http://localhost:8081/
DATABASE_HOSTNAME=db
DATABASE_DATABASE=start_cms_db
DATABASE_USER=ci_user
DATABASE_PASSWORD=ci_pass
```

### Troubleshooting

**Issue**: Containers not starting
```bash
# Check for port conflicts
docker ps
# Remove and rebuild
docker compose down -v
docker compose up --build -d
```

**Issue**: Database connection error
- Ensure MySQL container is running: `docker ps`
- Verify `.env` file has correct database credentials
- Check database is initialized: `docker logs ci_mysql57`

**Issue**: Permission denied on uploads
- The application automatically sets proper permissions on startup
- If issues persist, rebuild with: `docker compose down -v && docker compose up --build -d`

## Development

### Running Commands in Container

```bash
# Access PHP container shell
docker exec -it ci_php56 bash

# Run Composer commands
docker exec ci_php56 composer install

# Generate password hash
docker exec ci_php56 php -r "echo password_hash('your_password', PASSWORD_BCRYPT);"
```

### Database Backup/Restore

```bash
# Backup database
docker exec ci_mysql57 mysqldump -u ci_user -pci_pass start_cms_db > backup.sql

# Restore database
docker exec -i ci_mysql57 mysql -u ci_user -pci_pass start_cms_db < backup.sql
```

## API Documentation

The application includes a REST API. Check the API documentation at:
- http://localhost:8081/api/v1 (API endpoints)
- See `Start CMS API.postman_collection.json` for Postman collection

## Support

For issues, feature requests, or contributions, please visit the GitHub repository.

## License

This project is licensed under the GNU License - see the LICENSE.txt file for details.

### Tech

startCodeIgniter CSM uses a number of open source projects to work properly:

-   [VueJS](https://github.com/vuejs/vue) - HTML enhanced for web apps!
-   [tinymce] - awesome web-based text editor
-   [Materialize] - great UI boilerplate for modern web apps
-   [MySQL] - the popular database for storage
-   [Codeigneiter](https://github.com/bcit-ci/CodeIgniter) - fast PHP app framework
-   [Gulp](https://chat.openai.com/http) - the streaming build system
-   [jQuery](http://jquery.com/) - duh

And of course startCodeIgniter CSM itself is open source with a [public repository](https://github.com/gervisbermudez/startCodeIgniter-CSM) on GitHub.

### Installation

startCodeIgniter CSM requires [Composer](https://getcomposer.org/).

Install the dependencies and devDependencies and start the server.

```sh
$ git clone https://github.com/gervisbermudez/startCodeIgniter-CSM.git
$ cd ./startCodeIgniter-CSM
$ composer install
$ npm install
$ php -S localhost:8000 -t ./ 
```
• startCodeIgniter CSM private panel admin will be in [/admin](https://localhost:8000/admin/). • startCodeIgniter CSM public website will be in [/](https://localhost:8000/).

### Development

Want to contribute? Great!

startCodeIgniter CSM uses Gulp for fast developing. Make a change in your file and instantaneously see your updates!

Open your favorite Terminal and run these commands.

First Tab:

```sh
$ php -S localhost:8000 -t ./ 
```

Second Tab:

```sh
$ gulp watch_resources
```

### Todos

-  Write MORE Tests
- Add support for multiple languages
- Add support for more file types
- Improve security
- Improve API documentation

## License

MIT

**Free Software, Hell Yeah!**

[//]: # "These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax"
[startcodeigniter]: https://github.com/gervisbermudez/startCodeIgniter-CSM
[git-repo-url]: https://github.com/gervisbermudez/startCodeIgniter-CSM.git
[df1]: http://daringfireball.net/projects/markdown/
[codeigneiter]: https://github.com/bcit-ci/CodeIgniter
[node.js]: http://nodejs.org
[twitter bootstrap]: http://twitter.github.com/bootstrap/
[jquery]: http://jquery.com
[@tjholowaychuk]: http://twitter.com/tjholowaychuk
[vuejs]: https://github.com/vuejs/vue
[gulp]: http://gulpjs.com
[materialize]: https://github.com/Dogfalo/materialize
