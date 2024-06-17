# WSL

###### <navbar>[Docs](/docs/) | [Installing](/docs/Readme) | WSL</navbar>

WSL (Windows Subsystem for Linux) is a great development environment for PHP

## Install WSL
  * From elevate prompt : `wsl --install`
  * My preference is Alpine Linux, get it from the Microsoft Store and follow the prompts to start it.

## Setup Alpine Dev environment

### as root

```sh
apk add sudo bash mc
```

* Set up sudo
  * modify /etc/sudoers
  * modify the %wheel line ..

### set distributions default users name

* exit the distribution and from windows command line
  * assuming you are running alpine
```
alpine.exe config --default-user <username>
```

### re-enter wsl

### modify /etc/apk/repositories to point to the latest stable repositories

<pre>
http://dl-cdn.alpinelinux.org/alpine/latest-stable/main
http://dl-cdn.alpinelinux.org/alpine/latest-stable/community
</pre>


```sh
sudo apk apk update
sudo apk upgrade

sudo apk add git rsync sassc unzip php php-phar php-iconv php-curl \
  php-ctype php-fileinfo php-posix php-session \
  php-dom php-openssl php-sqlite3 php-pear \
  php-tokenizer php-common \
  php-mysqlnd php-mbstring \
  php-xmlreader php-exif php-gd php-json php-xml \
  php-imap php-zip php-apache2 \
  php-mysqli php-simplexml php-xmlwriter \
  php-pecl-apcu php-pecl-imagick
```

depending on actual version of php (at the time of writing alpine is 8.3)

```sh
sudo apk add php83-dev php83-pecl-mailparse
```

Install Composer : https://getcomposer.org/
Follow the instruction at getcomposer.org

```
composer -V # should return version
```

update your prompt - create a .profile file
```
PATH="~/bin:$PATH"

export COLORTERM=truecolor

export PS1='\[\033[01;32m\]\u@$WSL_DISTRO_NAME\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '
```

good to go !
