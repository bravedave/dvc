#!/usr/bin/env sh
set -eu

SCRIPT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)

# Directory to store Mermaid.js and associated files
TARGET_DIR="src/bravedave/dvc/resources/tailwind"
mkdir -p "$TARGET_DIR"

TARGET="$TARGET_DIR/tailwind.min.css"
VERSION="${1:-latest}"
URL="https://cdn.jsdelivr.net/npm/tailwindcss@${VERSION}/dist/tailwind.min.css"
TMP_FILE=$(mktemp)

cleanup() {
  rm -f "$TMP_FILE"
}
trap cleanup EXIT INT TERM

if ! command -v curl >/dev/null 2>&1 && ! command -v wget >/dev/null 2>&1; then
  echo "Error: curl or wget is required." >&2
  exit 1
fi

echo "Downloading Tailwind CSS from: $URL"
if command -v curl >/dev/null 2>&1; then
  curl -fL "$URL" -o "$TMP_FILE"
else
  wget -qO "$TMP_FILE" "$URL"
fi

if [ ! -s "$TMP_FILE" ]; then
  echo "Error: downloaded file is empty." >&2
  exit 1
fi

if [ -f "$TARGET" ]; then
  cp "$TARGET" "$TARGET.bak"
  echo "Backup created: $TARGET.bak"
fi

mkdir -p "$(dirname -- "$TARGET")"
mv "$TMP_FILE" "$TARGET"

echo "Updated: $TARGET"
echo "Source:  $URL"

echo "Done."
