# WSL

###### <navbar>[Docs](/docs/) | [Installing](/docs/Readme) | WSL</navbar>

WSL (Windows Subsystem for Linux) is a great development environment for PHP

## Install WSL
  * From elevate prompt : `wsl --install`
  * This version is for Debian Linux, get it from the Microsoft Store and follow the prompts to start it.

## Setup Debian Dev environment

### update before you start

```sh
sudo apt update
sudo apt upgrade
```

### my prefered edit is midnight commander, so ...

```sh
sudo apt install mc
```

### install php etc ...

```sh
sudo apt install git sqlite3 rsync sassc unzip curl wget composer \
  php php-mailparse libapache2-mod-php
a2enmod mpm_worker
```

update your prompt - create a .profile file
```
PATH="~/bin:$PATH"

export COLORTERM=truecolor

export PS1='\[\033[01;32m\]\u@$WSL_DISTRO_NAME\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '
```

good to go !
