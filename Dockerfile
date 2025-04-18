FROM php:8.1-apache

# Enable Apache rewrite module (optional but useful)
RUN a2enmod rewrite

# Copy all your project files into Apache's web root
COPY . /var/www/html/

# Expose port 80 (standard web port)
EXPOSE 80
    