# News Aggregator API

This is a Laravel-based News Aggregator API that aggregates news articles from various sources, provides user authentication, and allows users to set preferences for personalized news feeds. The application is set up using Docker for a consistent and reproducible development environment.

## Table of Contents

- [Setup Instructions](#setup-instructions)
- [Running the Docker Environment](#running-the-docker-environment)
- [API Documentation](#api-documentation)
- [Features](#features)
- [Additional Notes](#additional-notes)

## Setup Instructions

### Prerequisites

Before setting up the project, ensure you have the following installed on your machine:

- [Docker](https://www.docker.com/products/docker-desktop) (Docker Desktop)
- [Docker Compose](https://docs.docker.com/compose/install/) (included with Docker Desktop)
- [Composer](https://getcomposer.org/)

### Step-by-Step Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/news-aggregator-api.git
   cd news-aggregator-api
   ```

2. **Install Laravel dependencies**: Run the following command to install all the necessary dependencies using Composer:
    ```bash
    composer install
    ```

3. **Create the .env file**: Copy the .env.example file to create a new .env file:
    ```bash
    cp .env.example .env
    ```

4. **Generate an application key**: This key is required by Laravel to secure your application.
    ```bash
    php artisan key:generate
    ```

5. **Set up database credentials in .env**: Make sure the .env file is updated to connect to the MySQL container:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=root
    ```

6. **Run database migrations**: Run the following Artisan command to set up the database structure:
    ```bash
    php artisan migrate
    ```

## Running the Docker Environment

The application uses Docker to provide a consistent development environment. The Docker setup includes services for Laravel (PHP-FPM), MySQL, and Nginx.

> **Note:** I'm not sure the Docker setup will work perfectly for everyone. If you encounter issues with Docker, you can start the application using the Laravel built-in server with the following command:
> ```bash
> php artisan serve
> ```

### How to Run the Docker Environment

1. **Build and start the Docker containers**:
    ```bash
    docker-compose up --build
    ```
    This will build and start the containers. The application will be accessible at `http://localhost:8000`.

2. **Run Laravel commands inside the Docker container**: To run Artisan commands or Composer commands, enter the app container using:
    ```bash
    docker-compose exec app bash
    ```
    Then, inside the container, you can run commands like:
    ```bash
    php artisan migrate
    ```

3. **Stop the Docker environment**: To stop the containers, run:
    ```bash
    docker-compose down
    ```

4. **Run Docker in detached mode**: To run the Docker containers in the background:
    ```bash
    docker-compose up -d
    ```

## API Documentation

The API documentation is available via Swagger. You can view the documentation by visiting the following URL:

- [API Documentation](http://localhost:8000/api/documentation) (local environment)

The Swagger configuration file is included in the repository. You can find it in the `storage/api-docs/api-docs.json` file.

The documentation includes details about the available API endpoints for authentication, articles, user preferences, and password reset.

## Features

- **User Authentication**: Register, log in, and log out users via API endpoints.
- **Password Reset**: Users can request password reset links and reset passwords using tokens.
- **Article Management**: Fetch articles from multiple sources (like The Guardian and The New York Times).
- **Personalized News Feed**: Users can set preferences for news sources, categories, and authors to get a personalized feed.
- **Caching**: The API uses caching for frequently requested endpoints to improve performance.
- **Rate Limiting**: Rate limits are applied to API endpoints to prevent abuse.
- **Search Functionality**: Users can search articles by keyword, category, source, and date.

## Additional Notes

- **Caching**: The application uses Laravel’s caching system to cache API responses for articles and user feeds. Cache is cleared when new articles are fetched or when user preferences are updated.
  
- **Rate Limiting**: The API is rate-limited to prevent abuse. You can configure the limits in `app/Providers/RouteServiceProvider.php`.

- **Password Reset**: Users can request a password reset link via the `/api/password/email` endpoint and reset their password using the token sent in the email.

- **Swagger Integration**: The API is documented using Swagger, and the documentation can be regenerated using the following command:
    ```bash
    php artisan l5-swagger:generate
    ```

---
