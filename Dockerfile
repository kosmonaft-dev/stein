FROM dunglas/frankenphp

# Be sure to replace "your-domain-name.example.com" by your domain name
# ENV SERVER_NAME=your-domain-name.example.com
# If you want to disable HTTPS, use this value instead:
ENV SERVER_NAME=:80

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the PHP files of your project in the public directory
COPY . /app

# Set working directory
WORKDIR /app

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

# Command to run the application
CMD ["frankenphp", "serve", "--config", "/app/public/worker.php"]