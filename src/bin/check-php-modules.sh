#!/usr/bin/env bash
#
# David Bray
# BrayWorth Pty Ltd
# e. david@brayworth.com.au
#
# MIT License
#


# List of required PHP modules for the app
REQUIRED_MODULES=(
  apcu
  calendar
  curl
  dom
  exif
  ffi
  fileinfo
  filter
  ftp
  gd
  gettext
  hash
  iconv
  imagick
  imap
  intl
  json
  libxml
  mailparse
  mbstring
  openssl
  pcntl
  pcre
  pdo
  pdo_sqlite
  phar
  posix
  random
  readline
  reflection
  session
  shmop
  simplexml
  soap
  sockets
  sodium
  spl
  sqlite3
  standard
  sysvmsg
  sysvsem
  sysvshm
  tokenizer
  xml
  xmlreader
  xmlwriter
  xsl
  zip
  zlib
)

# Get the list of loaded PHP modules (lowercased, without spaces)
PHP_MODULES=$(php -m | grep -Ev '^\[|^$' | tr '[:upper:]' '[:lower:]' | tr -d ' ')

MISSING_MODULES=()

for module in "${REQUIRED_MODULES[@]}"; do
  # lowercase and remove spaces for matching
  mod_lc=$(echo "$module" | tr '[:upper:]' '[:lower:]' | tr -d ' ')
  if ! grep -qx "$mod_lc" <<< "$PHP_MODULES"; then
    MISSING_MODULES+=("$module")
  fi
done

if [ "${#MISSING_MODULES[@]}" -eq 0 ]; then
  echo "All required PHP modules are installed."
  exit 0
else
  echo "The following PHP modules are missing:"
  for mod in "${MISSING_MODULES[@]}"; do
    echo "  - $mod"
  done
  echo
  echo "To install missing modules:"
  echo "  - The package name may differ depending on your distribution."
  echo "  - You may need to prefix the command with 'sudo'."
  echo "  - Common commands:"
  echo "      Debian/Ubuntu:   apt install php-<module>"
  echo "      Fedora/RHEL:     dnf install php-<module>"
  echo "      Alpine:          apk add php<module>"
  echo "      macOS (brew):    brew install php"
  echo
  echo "Example for Debian/Ubuntu:"
  echo "    sudo apt install php-"$(echo "${MISSING_MODULES[@]}" | tr ' ' ' php-')
  echo
  echo "If you are using a different distribution, consult your package manager's documentation."
  exit 1
fi