#!/bin/bash

set -e
basedir="src/bravedave/dvc/js/preact"
[ -d $basedir ] || mkdir -p $basedir

echo "🔄 Updating Preact and HTM modules..."

# Resolve and download actual file, capturing version from redirect
download_with_version() {
  local name=$1
  local url=$2
  local target=$3

  # Capture redirect URL
  full_url=$(curl -sI "$url" | grep -i '^location:' | awk '{print $2}' | tr -d '\r')

  if [[ $full_url =~ @([0-9]+\.[0-9]+\.[0-9]+) ]]; then
    version="${BASH_REMATCH[1]}"
  else
    version="unknown"
  fi

  curl -sSL "$url" -o "$target"
  echo " - $name: v$version"
}

# Download Preact's official browser-ready modules
download_with_version "preact" "https://unpkg.com/preact@latest/dist/preact.module.js" $basedir/preact.module.js
download_with_version "hooks" "https://unpkg.com/preact@latest/hooks/dist/hooks.module.js" $basedir/hooks.module.js
download_with_version "htm" "https://unpkg.com/htm@latest/dist/htm.module.js" $basedir/htm.module.js

echo "✅ Done."
