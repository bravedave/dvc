#!/bin/bash

# Directory to store Quill.js and associated files
TARGET_DIR="src/bravedave/dvc/resources/quill"
mkdir -p "$TARGET_DIR"

# Download the latest version of Quill.js
echo "Fetching the latest version of Quill.js..."
QUILL_LATEST_VERSION=$(curl -sSL https://api.github.com/repos/quilljs/quill/releases/latest | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/')
QUILL_JS_URL="https://cdn.jsdelivr.net/npm/quill/dist/quill.js"
QUILL_CSS_URL="https://cdn.jsdelivr.net/npm/quill/dist/quill.snow.css"
curl -s -o "$TARGET_DIR/quill.js" "$QUILL_JS_URL"
curl -s -o "$TARGET_DIR/quill.snow.css" "$QUILL_CSS_URL"

# Download Quill Better Table extension
echo "Downloading Quill Better Table extension..."
BETTER_TABLE_URL="https://cdn.jsdelivr.net/npm/quill-better-table/dist/quill-better-table.min.js"
curl -s -o "$TARGET_DIR/quill-better-table.min.js" "$BETTER_TABLE_URL"

# Download Quill Resize Module
echo "Downloading Quill Resize Module..."
RESIZE_MODULE_URL="https://raw.githubusercontent.com/mudoo/quill-resize-module/refs/heads/master/dist/resize.js"
RESIZE_CSS_URL="https://raw.githubusercontent.com/mudoo/quill-resize-module/refs/heads/master/dist/resize.css"
curl -s -o "$TARGET_DIR/quill-resize.js" "$RESIZE_MODULE_URL"
curl -s -o "$TARGET_DIR/quill-resize.css" "$RESIZE_CSS_URL"

# Print the downloaded Quill.js version
echo "Quill.js version $QUILL_LATEST_VERSION downloaded successfully and stored in $TARGET_DIR."
echo "Quill Better Table extension downloaded successfully."
echo "Quill Resize Module downloaded successfully."