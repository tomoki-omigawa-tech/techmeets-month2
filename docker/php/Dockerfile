FROM php:8.2-fpm

# 必要なパッケージをインストール（WebP・JPEG対応のライブラリを含む）
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# PHP拡張機能をインストール（GDはWebP・JPEG対応でビルド）
RUN docker-php-ext-configure gd --with-webp --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリ
WORKDIR /var/www/html

# 権限設定
RUN chown -R www-data:www-data /var/www/html
# タイムゾーンを日本時間に設定
RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
