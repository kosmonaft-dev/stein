FROM dunglas/frankenphp

# Be sure to replace "your-domain-name.example.com" by your domain name
# ENV SERVER_NAME=your-domain-name.example.com
# If you want to disable HTTPS, use this value instead:
ENV SERVER_NAME=:80

# Port to expose on PaaS
ENV PORT=32768

# Set the worker command
ENV FRANKENPHP_CONFIG="worker ./public/worker.php"

# Set the environment variables
# Should be set in the PaaS directly
# ENV APP_ENV=production
# ENV APP_DEBUG=false
# ENV APP_URL=http://localhost:32768/

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install unzip
RUN apt-get update && apt-get install -y unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the PHP files of your project in the public directory
COPY . /app

# Set working directory
WORKDIR /app

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress