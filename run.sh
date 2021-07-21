#!/bin/bash

WD=`pwd`
PORT=$[RANDOM%1000+1024]
apache=`command -v httpd`

cd tests/www

if [[ "" == $apache ]]; then
  php=php
  if [[ -x /usr/bin/php8 ]]; then php=php8; fi

  echo "this application is available at http://localhost:$PORT"
  $php -S localhost:$PORT _dvc.php

else
  error_log="`pwd`/../application/data/error.log"
  config="`pwd`/../application/data/httpd.conf"
  [[ ! -f $error_log ]] || rm $error_log
  if [[ ! -f $config ]]; then
    cp $WD/httpd-minimal.conf $config
    echo "<Directory `pwd`>" >>$config
    echo "  AllowOverride all" >>$config
    echo "  Require all granted" >>$config
    echo "</Directory>" >>$config


  fi

  echo "this application is available at http://localhost:$PORT"
  httpd  -D FOREGROUND \
    -f $config \
    -c "DocumentRoot `pwd`" \
    -c "Listen $PORT" \
    -c "ErrorLog $error_log" \
    -c "CustomLog `pwd`/../application/data/access.log combined" \
    -c "PidFile `pwd`/../application/data/httpd.pid"

fi
cd $WD
