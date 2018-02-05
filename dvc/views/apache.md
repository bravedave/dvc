# Apache

## .htaccess
```
# Necessary to prevent problems when using a controller named "index" and having a root index.php
# more here: http://httpd.apache.org/docs/2.2/content-negotiation.html
Options -MultiViews

# Disallows others to look directly into /public/ folder
Options -Indexes

RewriteEngine Off

FallbackResource /_dvc.php

php_value post_max_size 40M
php_value upload_max_filesize 40M

Header unset Pragma
Header unset Last-Modified
Header unset Cache-Control
```

## VirtualHost.conf
```
<VirtualHost *:80>
	ServerName dvc.your.dom

  # expose the public root, note we don't usually store anything here
  DocumentRoot /opt/data/core/[project]/www/

	AccessFileName .htaccess

	# this path needs to include the vendor files & the project files
	<Directory /opt/data/core/>
		AllowOverride All
		Require all granted
		php_admin_value open_basedir /opt/data/core/:/tmp/

	</Directory>

</VirtualHost>
```
