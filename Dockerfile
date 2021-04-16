FROM php:7.4-apache
RUN a2enmod rewrite
# Instalação de packages e ext.
RUN apt-get update \
  && apt-get install -y zlib1g-dev libzip-dev wget gnupg \
  && docker-php-ext-install zip opcache

# Instalação SQL Server ext.
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update
RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools
RUN apt-get install -y unixodbc-dev libgssapi-krb5-2
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable sqlsrv
RUN docker-php-ext-enable pdo_sqlsrv