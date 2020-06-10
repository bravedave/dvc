#!/bin/sh

WD=`pwd`

cd tests/www
echo "this application is available at http://localhost:8080"
php -S localhost:8080 _dvc.php
cd $WD
