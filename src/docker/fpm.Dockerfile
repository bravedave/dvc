# Alpine keeps containers lean: tiny base image,
# minimal attack surface, fast builds, fewer CVEs,
# quick pulls, predictable reproducibility.
#
# Perfect for PHP-FPM since you only add needed
# extensions, keeping memory and disk footprint low
# without bloated system packages.

FROM alpine:latest

# Set build args for user
ARG USERNAME=devuser
ARG UID=1000
ARG GID=1000

RUN addgroup -g $GID $USERNAME && \
  adduser -D -u $UID -G $USERNAME $USERNAME && \
  echo "$USERNAME ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

RUN echo "https://dl-cdn.alpinelinux.org/alpine/latest-stable/main" > /etc/apk/repositories \
  && echo "https://dl-cdn.alpinelinux.org/alpine/latest-stable/community" >> /etc/apk/repositories \
  && apk update \
  && apk upgrade

RUN apk add --no-cache \
    git sqlite rsync sassc unzip fcgi curl bash shadow tzdata tini mariadb-client \
    php84 \
    php84-bcmath \
    php84-cli \
    php84-ctype \
    php84-curl \
    php84-common \
    php84-dom \
    php84-fileinfo \
    php84-fpm \
    php84-exif \
    php84-gmp \
    php84-iconv \
    php84-intl \
    php84-gd \
    php84-json \
    php84-mbstring \
    php84-mysqli \
    php84-mysqlnd \
    php84-opcache \
    php84-pdo_mysql \
    php84-phar \
    php84-openssl \
    php84-pear \
    php84-posix \
    php84-session \
    php84-simplexml \
    php84-sockets \
    php84-sqlite3 \
    php84-tokenizer \
    php84-xml \
    php84-xmlreader \
    php84-xmlwriter \
    php84-zip \
    php84-pecl-apcu \
    php84-pecl-imagick \
    php84-pecl-mailparse \
    php84-pecl-imap \
    php84-pecl-ssh2 \
    && ln -s /usr/bin/php84 /usr/bin/php

    # Set timezone to Australia/Queensland
RUN cp /usr/share/zoneinfo/Australia/Queensland /etc/localtime && \
  echo "Australia/Queensland" > /etc/timezone

# Set root's shell to bash (my preference)
RUN chsh -s /bin/bash root
RUN chsh -s /bin/bash $USERNAME

# FPM config tweak
# awk '!/^\s*;/ && !/^\s*$/' /etc/php84/php-fpm.d/www.conf
RUN mkdir -p /run/php && \
  sed -i 's|^;*listen =.*|listen = 0.0.0.0:9000|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*clear_env = no|clear_env = no|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*daemonize = yes|daemonize = no|' /etc/php84/php-fpm.conf && \
  sed -i 's|^;*pm.status_path =.*|pm.status_path = /status|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*ping.path =.*|ping.path = /ping|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*error_log =.*|error_log = /proc/self/fd/2|' /etc/php84/php-fpm.conf && \
  sed -i 's|^;*catch_workers_output = yes|catch_workers_output = yes|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*php_admin_flag\[log_errors\] = on|php_admin_flag[log_errors] = on|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*php_admin_value\[error_log\] =.*|php_admin_value[error_log] = /proc/self/fd/2|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*pm.max_children =.*|pm.max_children = 20|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*user =.*|user = '$USERNAME'|' /etc/php84/php-fpm.d/www.conf && \
  sed -i 's|^;*group =.*|group = '$USERNAME'|' /etc/php84/php-fpm.d/www.conf

# Copy custom PHP ini
COPY docker/php.ini /etc/php84/conf.d/99-custom.ini

# HEALTHCHECK: ask php-fpm if it’s alive
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
      cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

# Use tini as the init process (handles PID 1 properly, signals, zombies)
ENTRYPOINT ["/sbin/tini", "--"]

USER $USERNAME
CMD ["php-fpm84", "-F"]
