#!/bin/bash
# this will
# 1. install to working folder bootstrap5,
# 2. Sync in bs5 scss
# 3. compile changes
# 4. create themed bs files in src/dvc/css/bootstrap5

if [ -x "$(command -v sassc)" ]; then

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

    # cat ../../bootstrap-custom.scss bootstrap.scss >bootstrap-custom.scss
    cat ../../bootstrap-custom.scss >bootstrap-custom.scss
    sassc --omit-map-comment -t expanded bootstrap-custom.scss $targetDir/bootstrap.css
    sassc --omit-map-comment -t compressed bootstrap-custom.scss $targetDir/bootstrap.min.css
    echo "$me : wrote bootstrap.min.css"

    cat ../../bootstrap-pink.scss >bootstrap-pink.scss
    sassc --omit-map-comment -t expanded bootstrap-pink.scss $targetDir/bootstrap-pink.css
    sassc --omit-map-comment -t compressed bootstrap-pink.scss $targetDir/bootstrap-pink.min.css
    echo "$me : wrote bootstrap-pink.min.css"

    cat ../../bootstrap-blue.scss bootstrap.scss >bootstrap-blue.scss
    sassc --omit-map-comment -t compressed bootstrap-blue.scss $targetDir/bootstrap-blue.min.css
    echo "$me : wrote bootstrap-blue.min.css"

    echo '@import "bootstrap";'>bootstrap-orange.scss
    cat ../../bootstrap-orange-bs5.scss >>bootstrap-orange.scss
    # cat ../../bootstrap-orange.scss bootstrap.scss >bootstrap-orange.scss
    sassc --omit-map-comment -t compressed bootstrap-orange.scss $targetDir/bootstrap-orange.min.css

    # cd ../../
    # targetDir="../bravedave/dvc/css/bootstrap5/"
    # sassc --omit-map-comment -t compressed bootstrap-orange-bs5.scss $targetDir/bootstrap-orange.min.css
    # echo "$me : wrote bootstrap-orange.min.css"
  else

    echo "rsync command not found .."
  fi
else

	echo "sassc command not found .."
fi
