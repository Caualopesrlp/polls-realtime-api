FROM php:8.3-fpm

# Definir diretório de trabalho
WORKDIR /var/www

# Instalar dependências do sistema Linux
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Limpar cache de pacotes do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões do PHP necessárias para Laravel e PostgreSQL
RUN docker-php-ext-install pdo_pgsql pgsql bcmath zip pcntlp

# Copiar o executável do Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar arquivos do projeto para o container
COPY . /var/www

# Garante permissões adequadas para pastas de escrita do Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
