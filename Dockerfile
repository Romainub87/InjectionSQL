# Use the official PHP image with Apache
FROM php:8.1-apache

# Install dependencies for SQLite3
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    pkg-config

# Enable SQLite3 extension
RUN docker-php-ext-install pdo_sqlite

# Copy the application files to the container
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/