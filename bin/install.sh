#!/bin/bash

# Start CMS 3.0 — start the PHP/Apache container (MySQL is on the host)

cd "$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"

echo "=========================================="
echo "Start CMS 3.0 — Docker (web only)"
echo "=========================================="
echo ""

if ! command -v docker &> /dev/null; then
    echo "Docker is not installed."
    echo "Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

if ! docker compose version &> /dev/null; then
    echo "Docker Compose plugin not found (need: docker compose)."
    echo "Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

echo "Docker Compose found"
echo ""

if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "Created .env from .env.example"
    else
        echo "Missing .env (and no .env.example). Aborting."
        exit 1
    fi
fi

echo "Starting ci_php56 on port 8081..."
echo "MySQL is not started by Compose. It must already run on the host."
echo ""
docker compose up -d

echo "Waiting for Apache..."
sleep 8

if docker ps --format '{{.Names}}' | grep -qx 'ci_php56'; then
    echo ""
    echo "=========================================="
    echo "Web container is up"
    echo "=========================================="
    echo ""
    echo "Site:    http://localhost:8081"
    echo "Admin:   http://localhost:8081/admin/login"
    echo "User:    gerber"
    echo "Password: admin123"
    echo ""
    echo "Database (host MySQL, not a Compose service):"
    echo "  Host in .env:  host.docker.internal (from the container)"
    echo "  Database:      start_cms_db"
    echo ""
    echo "Logs:    docker logs -f ci_php56"
    echo "Stop:    docker compose down   # does not delete MySQL data"
    echo ""
else
    echo "ci_php56 did not start. Check: docker logs ci_php56"
    exit 1
fi
