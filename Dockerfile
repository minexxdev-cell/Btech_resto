FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache for Cloud Run (port 8080)
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Update DocumentRoot if needed
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf

# Enable .htaccess overrides
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker.conf \
    && a2enconf docker

# Expose port 8080 (Cloud Run requirement)
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1

# Start Apache
CMD ["apache2-foreground"]