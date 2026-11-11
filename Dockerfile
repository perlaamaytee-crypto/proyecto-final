FROM php:8.3-apache
RUN docker-php-ext-install mysqli pdo_mysql
COPY . /var/www/html/
# La carpeta de fotos debe poder escribirla Apache, y se activan los .htaccess
RUN mkdir -p /var/www/html/foto/subidas \
 && chown -R www-data:www-data /var/www/html/foto/subidas \
 && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
ENV PORT=10000
RUN sed -i 's/80/${PORT}/g' \
 /etc/apache2/sites-available/000-default.conf \
 /etc/apache2/ports.conf