FROM php:8.1-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy your app code
COPY . /var/www/html/

# Expose port 80
EXPOSE 80

# Start Apache when container runs
CMD ["apache2-foreground"]
