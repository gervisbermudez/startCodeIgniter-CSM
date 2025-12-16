#!/bin/bash

# startCodeIgniter CSM - Quick Installation Script
# This script sets up the application with Docker

echo "=========================================="
echo "startCodeIgniter CSM - Docker Setup"
echo "=========================================="
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    echo "Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    echo "Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

echo "✅ Docker and Docker Compose found"
echo ""

# Start the application
echo "📦 Starting Docker containers..."
docker compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to start (this may take a minute)..."
sleep 10

# Check if containers are running
if docker ps | grep -q "ci_php56" && docker ps | grep -q "ci_mysql57"; then
    echo "✅ Containers started successfully"
    echo ""
    echo "=========================================="
    echo "✨ Installation Complete!"
    echo "=========================================="
    echo ""
    echo "🌐 Access the application:"
    echo "   Frontend:  http://localhost:8081"
    echo "   Admin:     http://localhost:8081/admin/login"
    echo ""
    echo "🔐 Default Credentials:"
    echo "   Username: gerber"
    echo "   Password: admin123"
    echo ""
    echo "📊 Database Access:"
    echo "   Host:     localhost"
    echo "   Port:     3306"
    echo "   User:     ci_user"
    echo "   Password: ci_pass"
    echo "   Database: start_cms_db"
    echo ""
    echo "💡 Useful Commands:"
    echo "   Stop:     docker compose down"
    echo "   Logs:     docker logs ci_php56"
    echo "   Restart:  docker compose restart"
    echo ""
else
    echo "❌ Failed to start containers"
    echo "Run 'docker logs ci_php56' to check the error"
    exit 1
fi
