# Family Manager

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

**Important:**  
After seeding, the default admin username and password are both `admin`.  
Be sure to log in and update your admin password immediately for security.

## Getting Started

### Build the Docker Container

```bash
docker-compose up -d --build
```

### Seed the Database

Enter the web container and run:

```bash
php config/migrate.php
```

seed database with a few users

```bash
php config/seeder.php

## Run Tests (PHPPest)

from project directory.

```bash
./vendor/bin/pest
```

### Access the Application

Open your browser and navigate to `http://localhost:8080` to use the php boilerplate.

Access phpmyadmin in your browser at `http://localhost:8081`

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for enhancements