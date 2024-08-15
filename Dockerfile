# Use an official PHP image as the base
FROM php:7.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy application code to the container
COPY . /var/www/html

# Copy the Apache configuration file
COPY apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Copy the setup script and make it executable
COPY setup.sh /setup.sh
RUN chmod +x /setup.sh

# Expose port 80 for web traffic
EXPOSE 80

# Set the entrypoint to run the setup script and then start Apache
ENTRYPOINT ["/setup.sh"]

# Start Apache in the foreground
CMD ["apache2-foreground"]
