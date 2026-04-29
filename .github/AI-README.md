# AI README for .github AI Governance

This document explains how to run AI workflows effectively in the DVC repository.

## Contextual Governance Convention

`README.md` in any folder is the authoritative governance document for that folder and should always be read for contextual governance before planning or implementing changes in that folder.

Exception: in `.github/`, `AI-README.md` is the authoritative governance document to avoid default GitHub README rendering.

## Start Here

1. Read [copilot-instructions.md](copilot-instructions.md) for framework conventions and coding standards.
2. Read [AI-CODING-INDEX.md](AI-CODING-INDEX.md) for the complete AI asset map.
3. Pick the workflow below that matches your task.

## Core Workflows

### 1. Issue to Implementation (Delta Flow)

Use this when starting from an issue or plain requirement text.

1. Run [skills/issue-to-delta/SKILL.md](skills/issue-to-delta/SKILL.md) to produce or refine a delta plan.
2. Resolve open questions until the issue is implementation-ready.
3. Implement using [prompts/delta-implement-change.prompt.md](prompts/delta-implement-change.prompt.md).
4. If actions changed, enforce API parity using [prompts/api-contract-maintenance.standard.md](prompts/api-contract-maintenance.standard.md).

### 2. Execute Embedded Delta Task Blocks

Use this when code contains explicit delta task comments.

1. Run [prompts/delta-action-tasks.prompt.md](prompts/delta-action-tasks.prompt.md).
2. Implement all tasks in dependency order.
3. Replace the delta block with a concise implementation note.
4. Update module docs and API docs where required.

### 3. Build or Extend Modules

Use these targeted prompts depending on scope.

- [prompts/implement-simple-crud.prompt.md](prompts/implement-simple-crud.prompt.md) for straightforward CRUD modules.
- [prompts/implement-rich-workspace.prompt.md](prompts/implement-rich-workspace.prompt.md) for feed/workbench style modules.
- [prompts/implement-authorisation.prompt.md](prompts/implement-authorisation.prompt.md) for auth and permission flows.

### 4. Generate Module Documentation

Use [agents/create-comprehensive-AI-coding-instructions-for-model.agent.md](agents/create-comprehensive-AI-coding-instructions-for-model.agent.md).

Expected outputs:

- Module `README.md` documenting architecture and usage.
- Module `{module}.api.md` when `postHandler()` actions exist.

### 5. Convert Text Documents to Markdown

Use [skills/convert-plaintext-to-md/SKILL.md](skills/convert-plaintext-to-md/SKILL.md).

## API Contract Parity Checklist

Apply this whenever `postHandler()` actions are added, renamed, or removed.

- API file path: `src/app/{module}/{module}.api.md`
- Action parity: documented actions match `postHandler()` exactly
- Request parity: documented fields match handler expectations
- Response parity: documented payloads reflect actual `json::ack` / `json::nak` behavior
- Example parity: keep curl examples practical and current

Source of truth: [prompts/api-contract-maintenance.standard.md](prompts/api-contract-maintenance.standard.md)

## Quick Reference

| Need | Use |
|---|---|
| Plan change from issue text | [skills/issue-to-delta/SKILL.md](skills/issue-to-delta/SKILL.md) |
| Implement prepared delta | [prompts/delta-implement-change.prompt.md](prompts/delta-implement-change.prompt.md) |
| Execute inline delta tasks | [prompts/delta-action-tasks.prompt.md](prompts/delta-action-tasks.prompt.md) |
| Build simple CRUD module | [prompts/implement-simple-crud.prompt.md](prompts/implement-simple-crud.prompt.md) |
| Build rich workspace module | [prompts/implement-rich-workspace.prompt.md](prompts/implement-rich-workspace.prompt.md) |
| Add authorization features | [prompts/implement-authorisation.prompt.md](prompts/implement-authorisation.prompt.md) |
| Keep API docs in sync | [prompts/api-contract-maintenance.standard.md](prompts/api-contract-maintenance.standard.md) |
| Generate module docs | [agents/create-comprehensive-AI-coding-instructions-for-model.agent.md](agents/create-comprehensive-AI-coding-instructions-for-model.agent.md) |

## Maintenance Notes

- Keep file names stable for prompts, skills, and agents.
- If an AI asset is renamed, update references in:
  - [AI-CODING-INDEX.md](AI-CODING-INDEX.md)
  - [copilot-instructions.md](copilot-instructions.md)
  - Any affected prompt/skill documentation
- Keep examples and wording DVC-specific, not CMS-specific.
