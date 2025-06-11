# PHPが動作するApacheイメージを使用
FROM php:8.2-apache
# Apacheのmod_rewriteを有効にする（必要であれば）
# .htaccessを使用する場合や、URLリライトを行う場合に必要
RUN a2enmod rewrite

# PHPのエラーログ設定（オプション）
# 開発中にエラーを確認しやすくするため
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# コンテナのApacheドキュメントルートを/var/www/htmlに設定
# ここにホストのプロジェクトファイルをマウントする
WORKDIR /var/www/html

# ポート80を公開
EXPOSE 80