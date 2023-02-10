#!/bin/bash
# this will
# 1. install to working folder bootstrap4,
# 2. Sync in bs4 scss
# 3. compile changes
# 4. create themed bs files in src/dvc/css/bootstrap4

if [ -x "$(command -v sassc)" ]; then

	if [ -x "$(command -v rsync)" ]; then

    me=`basename "$0"`
    echo "start : $me"

    [ -d src/bravedave/dvc/css/bootstrap4 ] || mkdir -p src/bravedave/dvc/css/bootstrap4
    [ -d src/bravedave/dvc/js/bootstrap4 ] || mkdir -p src/bravedave/dvc/js/bootstrap4

    cd "$( dirname "${BASH_SOURCE[0]}" )"

    [ -d bootstrap4 ] || mkdir bootstrap4
    echo \*>bootstrap4/.gitignore

    composer req --working-dir=bootstrap4 twbs/bootstrap "<5"

    php bootstrap4JS.php
    cd bootstrap4

    rsync -a vendor/twbs/bootstrap/scss/./ scss/

    cd scss

    targetDir="../../../bravedave/dvc/css/bootstrap4/"

    cat ../../bootstrap-custom.scss bootstrap.scss ../../bootstrap4-polyfill.css >bootstrap-custom.scss
    sassc --omit-map-comment -t expanded bootstrap-custom.scss $targetDir/bootstrap.css
    sassc --omit-map-comment -t compressed bootstrap-custom.scss $targetDir/bootstrap.min.css
    echo "$me : wrote bootstrap.min.css"

    cat ../../bootstrap-pink.scss bootstrap.scss ../../bootstrap4-polyfill.css >bootstrap-pink.scss
    sassc --omit-map-comment -t compressed bootstrap-pink.scss $targetDir/bootstrap-pink.min.css
    echo "$me : wrote bootstrap-pink.min.css"

    cat ../../bootstrap-blue.scss bootstrap.scss ../../bootstrap4-polyfill.css >bootstrap-blue.scss
    sassc --omit-map-comment -t compressed bootstrap-blue.scss $targetDir/bootstrap-blue.min.css
    echo "$me : wrote bootstrap-blue.min.css"

    cat ../../bootstrap-orange.scss bootstrap.scss ../../bootstrap4-polyfill.css >bootstrap-orange.scss
    sassc --omit-map-comment -t compressed bootstrap-orange.scss $targetDir/bootstrap-orange.min.css
    echo "$me : wrote bootstrap-orange.min.css"
	else

		echo "rsync command not found .."
	fi
else

	echo "sassc command not found .."
fi
