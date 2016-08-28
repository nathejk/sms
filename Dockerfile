FROM ubuntu:16.04

WORKDIR /var/www

# Update and install packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends apache2 composer curl git libapache2-mod-php php-cli php-curl php-mysql php-xml php-zip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN echo "<VirtualHost *>\nDocumentRoot /var/www/public\nFallbackResource /index.php\n</VirtualHost>" > /etc/apache2/sites-enabled/000-default.conf

# Install app dependencies
ADD composer.* ./
RUN composer install --prefer-dist && \
    composer clearcache

# Copy site into place.
ADD . .

# Test image
RUN ./vendor/bin/phpunit src

#EXPOSE 80
#CMD /bin/bash -c "source /etc/apache2/envvars && exec /usr/sbin/apache2 -DFOREGROUND"
CMD ./console --no-interaction migrations:migrate && ./console sms:listen
#CMD sleep infinity
