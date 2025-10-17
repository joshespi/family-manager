# Family Manager

## Prerequisites

Make sure you have [Composer](https://getcomposer.org/) installed.

After cloning the repository, install PHP dependencies by running:

```bash
composer install
```

## Environment Configuration

Create a `.env` file with the following variables:

```yml
APP_VERSION=2.1.0
DB_HOST=famman_db
DB_PORT=33306
DB_ROOT_PASS=root
DB_NAME=familymanager
DB_USER=admin
DB_PASS=admin
DEV_MODE=true
```

> **Warning:**  
> Do **not** use these credentials in production environments. Update all sensitive values before deploying.

## Getting Started

### Build the Docker Container

```bash
docker-compose up -d --build
```

### Seed the Database

```bash
docker exec familymanager-famman_web-1 php config/migrate.php
```

**Important:**  
After seeding, the default admin username and password are both `admin`.  
Be sure to log in and update your admin password immediately for security.

seed database with a few users

```bash
docker exec familymanager-famman_web-1 php config/seeder.php
```

## Run Tests (PHPPest)

from project directory.

```bash
./vendor/bin/pest
```

## Static Analysis (PHPStan)

This project uses [PHPStan](https://phpstan.org/) for static code analysis.

To run PHPStan:

```bash
vendor/bin/phpstan analyse src tests
```

You can configure analysis settings in the `phpstan.neon` file at the project root.

### Access the Application

Open your browser and navigate to `http://localhost:8040` to use the php boilerplate.

Access phpmyadmin in your browser at `http://localhost:8045`

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for enhancements