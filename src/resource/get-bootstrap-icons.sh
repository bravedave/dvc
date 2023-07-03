#!/bin/bash

me=`basename "$0"`
cd "$( dirname "${BASH_SOURCE[0]}" )"

if [ -d bootstrap-icons ]; then
  rm -fR bootstrap-icons
fi

[ -d bootstrap_icons ] || mkdir bootstrap_icons
echo \*>bootstrap_icons/.gitignore

composer req --working-dir=bootstrap_icons twbs/bootstrap-icons

# cd bootstrap_icons
targetDir="../bravedave/dvc/css/bootstrap-icons/"
echo "syncing : $me"
rsync -arR --delete bootstrap_icons/vendor/twbs/bootstrap-icons/font/./ bootstrap_icons/vendor/twbs/bootstrap-icons/./icons/ bootstrap_icons/vendor/twbs/bootstrap-icons/./LICENSE $targetDir

echo "done : $me"
