FROM php:8.2-apache

# Install the SQLite database modules needed for your database file
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Move your project code to the web server's core execution directory
COPY . /var/www/html/

# Grant the web server access permissions to modify your local SQLite database file
RUN chown -W www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

EXPOSE 80
