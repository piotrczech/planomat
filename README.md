# planoMAT

planoMAT is an application designed to support communication between the dean's office and academic staff.

## About the project

The project consists of two main modules:

- **Desiderata** - a module enabling academic staff to submit their teaching preferences
- **Consultations** - a module for managing consultation hours during the semester and examination session

## Technical requirements

- Docker

## Development setup

1. Clone the repository:
```bash
git clone https://github.com/piotrczech/planomat.git
cd planomat
```

2. Copy the configuration file and fill in the important data:
```bash
cp .env.example .env
```

3. Install PHP dependencies:
```bash
composer install
```

4. Start the development environment using Laravel Sail:
```bash
./vendor/bin/sail up -d
```

5. Generate application key:
```bash
./vendor/bin/sail artisan key:generate
```

6. Run database migrations:
```bash
./vendor/bin/sail artisan migrate
```

7. Install frontend dependencies and build assets:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

The application will be available at: http://localhost

## Production environment

A separate Docker image is prepared for the production environment `docker-compose.prod.yml`

Database and storage volumes are external, so `docker compose down -v` does not remove them. On a fresh server create them once before the first start:
```bash
docker volume create planomat_db-data
docker volume create planomat_storage
```

Deploy with `docker/scripts/prod/restart.sh` (it dumps the database first), manual dump: `docker/scripts/prod/export-db.sh`. Dumps are saved to `backups/` and kept for 14 days (`KEEP_DAYS`).

## Technologies

- Laravel 11
- Livewire 3
- Alpine.js
- Tailwind CSS
- MySQL/MariaDB

## License

The project is open-source.
