#!/bin/bash

me=`basename "$0"`
cd "$( dirname "${BASH_SOURCE[0]}" )"

if [ -d bootstrap4 ]; then
  echo "no longer included"
  echo "should be removed .."
  rm -fR bootstrap4
fi
exit 0

echo "start : $me"

[ -d bootstrap-4.tmp ] || git clone --branch v4-dev https://github.com/twbs/bootstrap/ bootstrap-4.tmp
cd bootstrap-4.tmp
git pull --rebase

cd ..

rsync -arR --delete bootstrap-4.tmp/dist/./ bootstrap-4.tmp/./scss/ bootstrap4/

echo "done : $me"
