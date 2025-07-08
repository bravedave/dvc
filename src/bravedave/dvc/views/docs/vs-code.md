# VS-Code

Just some stuff I use in vs-code

```
.vscode               # VS Code Configuration files
  +-- tasks.json      # runnable tasks
```

## tasks.json

task to launch server in a normal development environment

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