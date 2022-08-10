# DVC - Data View Controller

> PHP framework for web applications and APIs.
Configured with Bootstrap4, but could just as easily support others.

* <https://brayworth.com/docs/>

## Development Install

* Install WSL
  * From elevate prompt : `wsl --install`
  * My preference is Alpine Linux, get it from the Microsoft Store and follow the prompts to start it.
  * Setup
    * change to root
    * modify /etc/apk/repositories to point to the latest stable repositories

<pre>
http://dl-cdn.alpinelinux.org/alpine/latest-stable/main
http://dl-cdn.alpinelinux.org/alpine/latest-stable/community
</pre>

```
sudo apk apk update
sudo apk upgrade
sudo apk add bash mc git rsync sassc unzip

sudo apk add php8 php8-phar php8-iconv php8-curl \
  php8-ctype php8-fileinfo php8-posix php8-session \
  php8-dom php8-openssl php8-sqlite3 php8-pear \
  php8-tokenizer php8-common php8-pecl-mailparse \
  php8-mysqlnd php8-pecl-imagick php8-mbstring php8-dev \
  php8-xmlreader php8-exif php8-gd php8-json php8-xml \
  php8-imap php8-pecl-apcu php8-zip php8-apache2 \
  php8-mysqli php8-simplexml php8-xmlwriter
```

* Install Composer : <https://getcomposer.org/>
  * Follow the instruction at getcomposer.org
  * Finish off with (from the bash prompt)
```
cd ~
mkdir bin
mv composer.phar bin/composer
```
* exit the distribution and re-enter, bin will be in the path so test with
```
composer -v # should return version
```

### Clone this Repo

```bash
git clone https://github.com/bravedave/dvc.git dvc
```

### Install dependencies

```bash
cd dvc
composer update
```

### Run the test Environment from ./tests

```bash
cd tests
run.cmd
```

... the tests are visible on <http://localhost/>

There is a tutorial [here](src/dvc/views/docs/risorsa.md)
