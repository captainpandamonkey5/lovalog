FROM php:8.2-apache

# Install mysqli extension for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files into Apache's web root
COPY . /var/www/html/

# Enable Apache mod_rewrite (needed for .htaccess)
RUN a2enmod rewrite

EXPOSE 80