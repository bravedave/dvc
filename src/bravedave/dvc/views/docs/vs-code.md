# VS-Code

Just some stuff I use in vs-code

```text
.vscode               # VS Code Configuration files
  +-- tasks.json      # runnable tasks
```

## tasks.json

> _task to launch server in a normal development environment_

```json
{
  "version": "2.0.0",
  "tasks": [
    {
      "label": "🚀 Launch Dev Server",
      "type": "shell",
      "command": "vendor/bin/dvc serve --port=8010",
      "problemMatcher": [],
      "isBackground": true,
      "presentation": {
        "reveal": "always",
        "panel": "new"
      }
    }
  ]
}
```

## Access the default db.sqlite

> _adds connection to sqlite in default location (requires SQL Tools)_

```json
{
  "sqltools.connections": [
    {
      "previewLimit": 50,
      "driver": "SQLite",
      "name": "db",
      "database": "src/data/db.sqlite"
    }
  ],
  "sqltools.useNodeRuntime": true,
  "sqltools.autoOpenSessionFiles": false
}
```
