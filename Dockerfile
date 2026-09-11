# =====================================================================
# PHP Backend Dockerfile — Student Management System
# =====================================================================
FROM php:8.2-cli-alpine

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

# Copy application files
COPY . .

# Port for PHP built-in server
EXPOSE 8001

# Default docroot points to phpdeck/project, can be customized via DOCROOT env
ENV DOCROOT=phpdeck/project

# Run PHP built-in server
CMD ["sh", "-c", "php -S 0.0.0.0:8001 -t ${DOCROOT}"]
