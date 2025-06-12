#!/bin/bash

# Directory to store Mermaid.js and associated files
TARGET_DIR="src/bravedave/dvc/resources/mermaid"
mkdir -p "$TARGET_DIR"

# Download the latest version of Mermaid.js
echo "Fetching the latest version of Mermaid.js..."
# MERMAID_LATEST_VERSION=$(curl -sSL https://api.github.com/repos/mermaid-js/mermaid/releases/latest | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/')
MERMAID_LATEST_VERSION=11
MERMAID_JS_URL="https://cdn.jsdelivr.net/npm/mermaid@${MERMAID_LATEST_VERSION}/dist/mermaid.min.js"
# MERMAID_CSS_URL="https://cdn.jsdelivr.net/npm/mermaid@${MERMAID_LATEST_VERSION}/dist/mermaid.min.css"

curl -s -o "$TARGET_DIR/mermaid.min.js" "$MERMAID_JS_URL"
# curl -s -o "$TARGET_DIR/mermaid.min.css" "$MERMAID_CSS_URL"

echo "Mermaid.js version $MERMAID_LATEST_VERSION downloaded successfully and stored in $TARGET_DIR."