FROM ubuntu:16.04

WORKDIR /var/www

# Update and install packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends apache2 ca-certificates curl libapache2-mod-php php7.0-cli php7.0-curl php-mysql php-xml php-zip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN echo "<VirtualHost *>\nDocumentRoot /var/www/public\nFallbackResource /index.php\n</VirtualHost>" > /etc/apache2/sites-enabled/000-default.conf

# Install app dependencies
ADD composer.* ./
RUN curl -sS https://getcomposer.org/installer | php && ./composer.phar install --prefer-dist && ./composer.phar clearcache

# Copy site into place.
ADD . .

# Test image
RUN ./vendor/bin/phpunit src

EXPOSE 80
CMD /bin/bash -c "source /etc/apache2/envvars && exec /usr/sbin/apache2 -DFOREGROUND"
