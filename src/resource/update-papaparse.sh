#!/bin/bash

# The latest version of papaparse is available at
# https://unpkg.com/papaparse@latest/papaparse.min.js
#
# This script downloads it and updates src/bravedave/dvc/js/papaparse.min.js if it changes

set -e

PAPAPARSE_URL="https://unpkg.com/papaparse@latest/papaparse.min.js"
TARGET_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../bravedave/dvc/js" && pwd)"
TARGET_FILE="$TARGET_DIR/papaparse.min.js"
TEMP_FILE=$(mktemp)

echo "Downloading papaparse from $PAPAPARSE_URL..."

# Download the latest version
if curl -fsSL "$PAPAPARSE_URL" -o "$TEMP_FILE"; then
  echo "✓ Download successful"
  
  # Check if target file exists
  if [ -f "$TARGET_FILE" ]; then
    # File exists, compare with diff
    if diff -q "$TARGET_FILE" "$TEMP_FILE" > /dev/null 2>&1; then
      echo "✓ papaparse is already up to date"
      rm "$TEMP_FILE"
    else
      echo "⚠ papaparse version differs, updating..."
      diff "$TARGET_FILE" "$TEMP_FILE" || true
      mv "$TEMP_FILE" "$TARGET_FILE"
      echo "✓ papaparse updated at $TARGET_FILE"
    fi
  else
    # File doesn't exist, create it
    echo "⚠ papaparse.min.js not found, creating new copy..."
    mv "$TEMP_FILE" "$TARGET_FILE"
    echo "✓ papaparse created at $TARGET_FILE"
  fi
else
  echo "✗ Failed to download papaparse"
  rm -f "$TEMP_FILE"
  exit 1
fi