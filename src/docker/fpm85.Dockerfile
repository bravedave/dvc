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
  && echo "https://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories \
  && apk update \
  && apk upgrade

RUN apk add --no-cache \
    git github-cli sqlite rsync sassc dart-sass unzip fcgi curl bash shadow \
    tzdata tini socat mariadb-client ffmpeg \
    php85 \
    php85-fpm \
    php85-bcmath \
    php85-cli \
    php85-ctype \
    php85-curl \
    php85-common \
    php85-dom \
    php85-fileinfo \
    php85-exif \
    php85-gmp \
    php85-iconv \
    php85-intl \
    php85-gd \
    php85-json \
    php85-mbstring \
    php85-mysqli \
    php85-mysqlnd \
    php85-pdo_mysql \
    php85-phar \
    php85-openssl \
    php85-pear \
    php85-posix \
    php85-session \
    php85-simplexml \
    php85-sockets \
    php85-sqlite3 \
    php85-tokenizer \
    php85-xml \
    php85-xmlreader \
    php85-xmlwriter \
    php85-zip \
    php85-pecl-apcu \
    php85-pecl-imagick \
    php85-pecl-mailparse \
    php85-pecl-imap \
    php85-pecl-ssh2

RUN [ ! -e /usr/bin/php ] && ln -s /usr/bin/php85 /usr/bin/php || true

    # Set timezone to Australia/Queensland
RUN cp /usr/share/zoneinfo/Australia/Queensland /etc/localtime && \
  echo "Australia/Queensland" > /etc/timezone

# Set root's shell to bash (my preference)
RUN chsh -s /bin/bash root
RUN chsh -s /bin/bash $USERNAME

# FPM config tweak
# awk '!/^\s*;/ && !/^\s*$/' /etc/php85/php-fpm.d/www.conf
RUN mkdir -p /run/php && \
  sed -i 's|^;*listen =.*|listen = 0.0.0.0:9000|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*clear_env = no|clear_env = no|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*daemonize = yes|daemonize = no|' /etc/php85/php-fpm.conf && \
  sed -i 's|^;*pm.status_path =.*|pm.status_path = /status|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*ping.path =.*|ping.path = /ping|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*error_log =.*|error_log = /proc/self/fd/2|' /etc/php85/php-fpm.conf && \
  sed -i 's|^;*catch_workers_output = yes|catch_workers_output = yes|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*php_admin_flag\[log_errors\] = on|php_admin_flag[log_errors] = on|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*php_admin_value\[error_log\] =.*|php_admin_value[error_log] = /proc/self/fd/2|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*pm.max_children =.*|pm.max_children = 20|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*user =.*|user = '$USERNAME'|' /etc/php85/php-fpm.d/www.conf && \
  sed -i 's|^;*group =.*|group = '$USERNAME'|' /etc/php85/php-fpm.d/www.conf

# Copy custom PHP ini
COPY cms/docker/php.ini /etc/php85/conf.d/99-custom.ini

# HEALTHCHECK: ask php-fpm if it’s alive
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
      cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

# Use tini as the init process (handles PID 1 properly, signals, zombies)
ENTRYPOINT ["/sbin/tini", "--"]

USER $USERNAME
CMD ["php-fpm85", "-F"]
