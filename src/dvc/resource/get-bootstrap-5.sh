#!/bin/bash

me=`basename "$0"`
cd "$( dirname "${BASH_SOURCE[0]}" )"

echo "start : $me"

[ -d bootstrap-5.tmp ] || git clone https://github.com/twbs/bootstrap/ bootstrap-5.tmp
cd bootstrap-5.tmp
git pull --rebase

cd ..

rsync -arR --delete bootstrap-5.tmp/dist/./ bootstrap-5.tmp/./scss/ bootstrap5/

echo "done : $me"
