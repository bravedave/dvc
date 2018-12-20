# Install on Fedora VPS
1. Get a VPS, my preference is Fedora
1. dnf remove cockpit* gssproxy ModemManager
1. dnf install mc htop install httpd mariadb php composer git
1. dnf update
1. dnf clean all
1. Firewall Config
  1. firewall-cmd --permanent --remove-service=cockpit
  1. firewall-cmd --permanent --add-service=http
  1. firewall-cmd --list-all
  1. firewall-cmd --state
1. disable selinux
  * mc -e /etc/sysconfig/selinux
  * set to disabled
1. reboot

## Get Framework
1. Create Workspace
  1. mkdir -p /opt/data/core
  1. chmod 777 /opt/data/core
  1. cd /opt/data/core
1. Get composer.json
  1. wget https://raw.githubusercontent.com/bravedave/dvc/master/example/composer.json
1. Create a User
  1. useradd [a user]
  1. chown [a user].[a user] composer.json
  1. su [a user] -c 'composer install'

## ready to deploy
* e.g.
1. cd /opt/data/core
1. cp -R vendor/bravedave/dvc/example/ [project]
1. mkdir -p --mode=0777 [project]/application/app/public/js
1. mkdir -p --mode=0777 [project]/application/app/public/css

## apache stuff
1. create apache config
1. start apache