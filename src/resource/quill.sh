#!/bin/bash

pwd=`pwd`

cd ../bravedave/dvc
curl -o js/quill.js https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js
curl -o css/quill.snow.css https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css

cd $pwd
