#!/bin/bash

if [ -x "$(command -v sassc)" ]; then
	if [ -x "$(command -v rsync)" ]; then
    # this will
    # 1. create a folder bootstrap4,
    # 2. Sync in bs4 scss
    # 3. compile changes
    # 4. create themed bs files

    me=`basename "$0"`
    cd "$( dirname "${BASH_SOURCE[0]}" )"

    echo "start : $me"

    rsync -a ../../../vendor/twbs/bootstrap/scss/./ bootstrap4/
    cd bootstrap4
    cat ../bootstrap-custom.scss bootstrap.scss >bootstrap-custom.scss
    sassc --omit-map-comment -t expanded bootstrap-custom.scss ../bootstrap.css
    sassc --omit-map-comment -t compressed bootstrap-custom.scss ../bootstrap.min.css
    echo "$me : wrote bootstrap.min.css"

    cat ../bootstrap-pink.scss bootstrap.scss >bootstrap-pink.scss
    sassc --omit-map-comment -t compressed bootstrap-pink.scss ../bootstrap-pink.min.css
    logger "$me : wrote bootstrap-pink.min.css"

    cat ../bootstrap-blue.scss bootstrap.scss >bootstrap-blue.scss
    sassc --omit-map-comment -t compressed bootstrap-blue.scss ../bootstrap-blue.min.css
    echo "$me : wrote bootstrap-blue.min.css"

    cat ../bootstrap-orange.scss bootstrap.scss >bootstrap-orange.scss
    sassc --omit-map-comment -t compressed bootstrap-orange.scss ../bootstrap-orange.min.css
    echo "$me : wrote bootstrap-orange.min.css"

	else
		echo "rsync command not found .."
	fi
else
	echo "sassc command not found .."
fi
