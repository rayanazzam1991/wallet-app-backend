# Wallet App Backend

This repository contains the Laravel backend for the Wallet App. It is containerized with [Laravel Sail](https://laravel.com/docs/sail) for a streamlined Docker-based local environment.

## Prerequisites
- [Docker](https://docs.docker.com/get-docker/) and Docker Compose.
- PHP and Composer installed locally if you want to run Artisan or Sail without using `./vendor/bin/sail` (optional).
- Node.js and npm for asset tooling.

## Environment setup
1. Copy the example environment file and adjust values as needed:
   ```bash
   cp .env.example .env
   ```
2. Set `APP_KEY` (run `php artisan key:generate` after dependencies are installed) and update database credentials. The Sail compose file maps MySQL and Redis ports from the container to your host, so the defaults in `.env.example` align with the Docker services (`DB_HOST=127.0.0.1`, `DB_PORT=3306`, `REDIS_HOST=127.0.0.1`, `REDIS_PORT=6379`).
3. If you use GitHub Codespaces/SAIL `.environment` files for secrets, mirror the same keys found in `.env.example` (e.g., `APP_KEY`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) into your `.environment` so Sail can populate them on startup.

## Install dependencies
```bash
composer install
```

## Running with Laravel Sail (Docker)
The repository includes a Sail definition (`compose.yaml`) that builds a PHP 8.4 application container and runs MySQL and Redis.

1. Start the containers:
   ```bash
   ./vendor/bin/sail up -d
   ```
   This binds the application to port `${APP_PORT:-80}` on your host and forwards MySQL `${FORWARD_DB_PORT:-3306}` and Redis `${FORWARD_REDIS_PORT:-6379}`.
2. Generate the application key if you have not yet:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```
3. Run database migrations and seeders:
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```
4. Tail logs:
   ```bash
   ./vendor/bin/sail logs -f
   ```
5. Stop containers when finished:
   ```bash
   ./vendor/bin/sail down
   ```

## Testing
Run the PHP test suite inside Sail:
```bash
./vendor/bin/sail test
```

## Common environment variables
Key settings from `.env.example` you may want to adjust:
- `APP_URL`: URL you use to access the app.
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: database name and credentials for the MySQL container.
- `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`: storage drivers used by Laravel.

For additional variables, review `.env.example`.
