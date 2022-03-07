#!/bin/bash

me=`basename "$0"`
cd "$( dirname "${BASH_SOURCE[0]}" )"

echo "start : $me"

[ -d bootstrap-5.tmp ] || git clone https://github.com/twbs/bootstrap/ bootstrap-5.tmp
cd bootstrap-5.tmp
git pull --rebase

cd ..

rsync -arR --delete bootstrap-5.tmp/dist/./ bootstrap-5.tmp/./scss/ bootstrap5/

cd bootstrap5/scss
cat ../../bootstrap-custom.scss bootstrap.scss >bootstrap-custom.scss
sassc --omit-map-comment -t compressed bootstrap-custom.scss ../bootstrap.min.css
rm bootstrap-custom.scss
echo "$me : wrote bootstrap.min.css"

cat ../../bootstrap-blue.scss bootstrap.scss >bootstrap-blue.scss
sassc --omit-map-comment -t compressed bootstrap-blue.scss ../bootstrap-blue.min.css
rm bootstrap-blue.scss
echo "$me : wrote bootstrap-blue.min.css"

cat ../../bootstrap-orange.scss bootstrap.scss >bootstrap-orange.scss
sassc --omit-map-comment -t compressed bootstrap-orange.scss ../bootstrap-orange.min.css
rm bootstrap-orange.scss
echo "$me : wrote bootstrap-orange.min.css"

cat ../../bootstrap-pink.scss bootstrap.scss >bootstrap-pink.scss
sassc --omit-map-comment -t compressed bootstrap-pink.scss ../bootstrap-pink.min.css
rm bootstrap-pink.scss
echo "$me : wrote bootstrap-pink.min.css"

echo "done : $me"
