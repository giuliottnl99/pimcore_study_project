# Pimcore Project Setup

This document provides step-by-step instructions to set up your Pimcore project using Docker.

## Prerequisites

Make sure you have the following installed on your machine:

- **Docker**
- **Docker Compose**

## Setup Instructions

### 1. Start Docker Containers

To initiate the Docker containers, run:

```bash
docker compose up -d
```

### 2. Access the Database Container

Once the containers are running, access the database container:

```bash
docker compose exec -it db bash
```

### 3. Import the Database

Import the initial database schema and data using the following command:

```bash
mysql -u pimcore -p pimcore < /application/basic-db.sql
```

**Password:** `pimcore`

After the import is complete, exit the database container:

```bash
exit
```

### 4. Access the PHP Container

Next, you need to access the PHP container:

```bash
docker compose exec php bash
```

### 5. Install Composer Dependencies

Inside the PHP container, run the following command to install the required Composer packages:

```bash
composer install
```

### 6. Rebuild Classes and Clear Cache

Run the following commands to rebuild Pimcore classes and clear the cache:

```bash
bin/console pimcore:deployment:classes-rebuild -v -c
bin/console pimcore:cache:clear
bin/console cache:clear
```

### 7. Configure Database Connection

If your database configuration is different, edit the database configuration file if you have done change: config/local/database.yaml


### 8. Access the Admin Panel

You can now access the Pimcore admin panel at:

```
http://localhost/admin
```

### Default Login Credentials

- **Username:** admin
- **Password:** admin

