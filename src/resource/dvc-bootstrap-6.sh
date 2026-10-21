#!/bin/bash
# this will
# 1. install to working folder bootstrap6,
# 2. minify bootstrap's js files without soucemaps to src/bravedave/dvc/js/bootstrap6

me=`basename "$0"`
echo "start : $me"

[ -d src/bravedave/dvc/css/bootstrap6 ] || mkdir -p src/bravedave/dvc/css/bootstrap6
[ -d src/bravedave/dvc/js/bootstrap6 ] || mkdir -p src/bravedave/dvc/js/bootstrap6

cd "$( dirname "${BASH_SOURCE[0]}" )"

[ -d bootstrap6 ] || mkdir bootstrap6
echo \*>bootstrap6/.gitignore

composer req --working-dir=bootstrap6 twbs/bootstrap "dev-main"

php bootstrap6JS.php
php bootstrap6CSS.php
