#!/bin/bash

me=`basename "$0"`
cd "$( dirname "${BASH_SOURCE[0]}" )"

echo "start : $me"

[ -d bootstrap-icons.tmp ] || git clone https://github.com/twbs/icons/ bootstrap-icons.tmp
cd bootstrap-icons.tmp
git pull --rebase

cd ..

rsync -arR bootstrap-icons.tmp/./icons/ bootstrap-icons.tmp/./font/ bootstrap-icons/

echo "done : $me"
