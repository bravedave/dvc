#!/bin/bash
# this will
# 1. install to working folder bootstrap5,
# 2. Sync in bs5 scss
# 3. compile changes
# 4. create themed bs files in src/dvc/css/bootstrap5
#
# This is using getbootstrap.com's recommendation for dart-sass : https://sass-lang.com/
# * https://getbootstrap.com/docs/5.3/customize/sass/
#
# Alpine Linux:
# at the time of writing the compiler is only available in the
# edge/testing repository - as this is the bleeding edge repo,
# recommended to disable it after use
#
# add the repo : https://dl-cdn.alpinelinux.org/alpine/edge/testing
# sudo apk update
# sudo apk add dart-sass
#
# Debian:
# https://github.com/sass/dart-sass
#

RED='\033[0;31m'
NC='\033[0m' # No Color

if [ -x "$(command -v sass)" ]; then

	if [ -x "$(command -v rsync)" ]; then

    me=`basename "$0"`
    echo "start : $me"

    [ -d src/bravedave/dvc/css/bootstrap5 ] || mkdir -p src/bravedave/dvc/css/bootstrap5
    [ -d src/bravedave/dvc/js/bootstrap5 ] || mkdir -p src/bravedave/dvc/js/bootstrap5

    cd "$( dirname "${BASH_SOURCE[0]}" )"

    [ -d bootstrap5 ] || mkdir bootstrap5
    echo \*>bootstrap5/.gitignore

    # composer req --working-dir=bootstrap5 twbs/bootstrap "^5"
    composer req --working-dir=bootstrap5 twbs/bootstrap "dev-main"

    php bootstrap5JS.php
    cd bootstrap5

    rsync -a vendor/twbs/bootstrap/scss/./ scss/

    cd scss

    targetDir="../../../bravedave/dvc/css/bootstrap5/"

    cat ../../bootstrap-custom.scss >bootstrap-custom.scss
    sass --no-source-map bootstrap-custom.scss $targetDir/bootstrap.css
    sass --no-source-map --style=compressed bootstrap-custom.scss $targetDir/bootstrap.min.css
    echo "$me : wrote bootstrap.min.css"

    cat ../../bootstrap-pink.scss >bootstrap-pink.scss
    sass --no-source-map bootstrap-pink.scss $targetDir/bootstrap-pink.css
    sass --no-source-map --style=compressed bootstrap-pink.scss $targetDir/bootstrap-pink.min.css
    echo "$me : wrote bootstrap-pink.min.css"

    cat ../../bootstrap-blue.scss >bootstrap-blue.scss
    sass --no-source-map bootstrap-blue.scss $targetDir/bootstrap-blue.css
    sass --no-source-map --style=compressed bootstrap-blue.scss $targetDir/bootstrap-blue.min.css
    echo "$me : wrote bootstrap-blue.min.css"

    cat ../../bootstrap-orange.scss >bootstrap-orange.scss
    sass --no-source-map bootstrap-orange.scss $targetDir/bootstrap-orange.css
    sass --no-source-map --style=compressed bootstrap-orange.scss $targetDir/bootstrap-orange.min.css
    echo "$me : wrote bootstrap-orange.min.css"
  else

    printf "${RED}---------------------------${NC}\n"
    printf "${RED}- rsync command not found -${NC}\n"
    printf "${RED}---------------------------${NC}\n"
  fi
else

	printf "${RED}--------------------------${NC}\n"
	printf "${RED}- sass command not found -${NC}\n"
	printf "${RED}--------------------------${NC}\n"
fi
