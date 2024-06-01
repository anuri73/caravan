FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/php

# Copy PHP script into container
COPY src/index.php .

# Expose port 80
EXPOSE 80

# Command to run the PHP script
CMD ["php", "-S", "0.0.0.0:80"]
