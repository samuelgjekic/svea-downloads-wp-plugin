# Use the official WordPress image from Docker Hub
FROM wordpress:latest

# Install any additional PHP extensions or software needed for testing
RUN docker-php-ext-install mysqli

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN apt-get update && apt-get install -y curl git unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the plugin files into the WordPress plugins directory
COPY ./svea-checkout-downloads /var/www/html/wp-content/plugins/svea-checkout-downloads

# Set permissions for the plugins directory
RUN chown -R www-data:www-data /var/www/html/wp-content/plugins/svea-checkout-downloads

# Run Composer to install dependencies
RUN cd /var/www/html/wp-content/plugins/svea-checkout-downloads && composer install

# Expose port 80
EXPOSE 80
