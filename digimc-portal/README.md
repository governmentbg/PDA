# Project Setup Guide

Clone the repository and navigate to the project folder:

```bash
  git clone <repository-url>
  cd <project-folder>
```

# Install dependencies. 
For development use:

```bash
  composer install
```
For production use:

```bash
  composer install --no-dev
```

# Copy the example environment file and configure your parameters:

```bash
  cp .env.example .env
```

Update the values inside .env for database, mail, and other required settings.

If you need to run secondary database migrations, enable it in your .env file by setting:
```angular2html
RUN_SECONDARY_MIGRATION=true
```
# Generate the application key (only once, never repeat this step):

```bash
  php artisan key:generate
```

# Run the database migrations:

```bash
  php artisan migrate
```

# Seed the database with initial data:

```bash
  php artisan db:seed
```

# To ensure that scheduled tasks run properly, set up the following cron job on your server:

```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```
