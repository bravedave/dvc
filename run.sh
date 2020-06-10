#!/bin/sh

WD=`pwd`
PORT=$[RANDOM%1000+1024]

cd tests/www
echo "this application is available at http://localhost:$PORT"
php -S localhost:$PORT _dvc.php
cd $WD
