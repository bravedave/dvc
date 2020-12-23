#!/bin/bash

me=`basename "$0"`

[ -d bootstrap-icons.tmp ] || git clone https://github.com/twbs/icons/ bootstrap-icons.tmp
cd bootstrap-icons.tmp
git pull

echo "done $me"
cd ..

rsync -arR --dry-run bootstrap-icons.tmp/./icons/ bootstrap-icons.tmp/./font/ bootstrap-icons/
