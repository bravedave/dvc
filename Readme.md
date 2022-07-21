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
apk apk update;apk upgrade
apk add bash mc git rsync php php_openssl php-phar \
  php-iconv php-curl php-ctype php-fileinfo \
  php-posix php-session php-dom sassc unzip
# probably you need to be specific about the version with this one
apk add php8-pecl-apcu
```

* Install Composer : <https://getcomposer.org/>
  * Foolow the instruction at getcomposer.org
  * Fihish off with (from the bash prompt)
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
