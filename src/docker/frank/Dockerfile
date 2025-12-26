FROM dunglas/frankenphp

RUN apt-get update && \
	apt-get install -y git zip unzip mariadb-client
# 
# # add additional extensions here:
RUN install-php-extensions \
	mysqli

# gd \
# intl \
# zip \
