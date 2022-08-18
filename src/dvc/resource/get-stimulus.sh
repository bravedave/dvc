#!/bin/bash

me=`basename "$0"`
cd "$( dirname "${BASH_SOURCE[0]}" )"

curl -O https://unpkg.com/@hotwired/stimulus/dist/stimulus.js
cp stimulus.js ../js/
rm stimulus.js

echo "done : $me"
