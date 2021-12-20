#!/bin/bash

WD=`pwd`
PORT=$[RANDOM%1000+1024]

apache=`command -v httpd`


if [[ "" == $apache ]]; then
  cd tests/www

  php=php
  if [[ -x /usr/bin/php8 ]]; then php=php8; fi

  echo "this application is available at http://localhost:$PORT"
  $php -S localhost:$PORT _mvp.php

  cd $WD

else
  data="`pwd`/tests/data"
  error_log="$data/error.log"
  access_log="$data/access.log"
  config="$data/httpd.conf"
  pidFile="$data/httpd.pid"

  [[ -d $data ]] || mkdir -p $data
  [[ -d $data ]] || exit 0

  if [ "$1" == "kill" ]; then
    if [[ -f $pidFile ]]; then
      kill `cat $pidFile`
      if [[ -f $pidFile ]]; then
        rm $pidFile

      fi

    fi

  else

    [[ ! -f $error_log ]] || rm $error_log
    [[ ! -f $access_log ]] || rm $access_log
    if [[ ! -f $config ]]; then
      cp $WD/httpd-minimal.conf $config
      echo "ErrorLogFormat \"[%t] %M\"" >>$config
      echo "ErrorLog $error_log" >>$config
      echo "CustomLog $access_log common" >>$config
      echo "DocumentRoot `pwd`/tests/www" >>$config
      echo "<Directory `pwd`/tests/www>" >>$config
      echo "  AllowOverride all" >>$config
      echo "  Require all granted" >>$config
      echo "</Directory>" >>$config

    fi

    if [[ -f $pidFile ]] ; then
      echo "running ..`cat $pidFile`"

    else
      echo "this application is available at http://localhost:$PORT"
      httpd \
        -f $config \
        -c "Listen $PORT" \
        -c "PidFile $data/httpd.pid"

    fi
    tail -f $data/error.log

  fi

fi
