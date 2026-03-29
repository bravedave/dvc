# AI README for DVC

This guide wires you up to use AI workflows in the DVC repository.

## Start Here

1. Read .github/copilot-instructions.md for framework conventions.
2. Read AI-CODING-INDEX.md for all available AI assets.
3. Use the workflow below that matches your task.

## Common Workflows

### 1) Issue to Implementation (Delta Flow)

Use this when starting from an issue description.

1. Create a plan from issue text with issue-to-delta skill.
2. Clarify open questions until readiness is READY for delta-implement-change.
3. Implement with .github/prompts/delta-implement-change.prompt.md.
4. Verify API parity using .github/prompts/api-contract-maintenance.standard.md.

### 2) Implement Embedded Delta Tasks

Use this when code contains structured delta task comments.

1. Run .github/prompts/delta-action-tasks.prompt.md.
2. Complete all numbered tasks.
3. Replace the delta block with a concise delta implemented note.
4. Update module README and API docs when actions changed.

### 3) Create Module Documentation and API Contract

Use the agent in .github/agents/create-comprehensive-AI-coding-instructions-for-model.agent.md.

Expected output:

- {module}/README.md architecture documentation.
- {module}/{module}.api.md when postHandler actions exist.

### 4) Convert Text Docs to Markdown

Use .github/skills/convert-plaintext-to-md/SKILL.md.

## API Contract Parity Checklist

Apply this whenever postHandler actions change:

- API file path: src/app/{module}/{module}.api.md
- Route source: src/controller/{module}.php
- Action parity: API list exactly matches postHandler
- Request parity: documented fields match handler expectations
- Examples: include practical curl calls

Source of truth: .github/prompts/api-contract-maintenance.standard.md

## Quick Reference

| Need | Use |
|---|---|
| Plan change from issue | .github/skills/issue-to-delta/SKILL.md |
| Implement planned delta | .github/prompts/delta-implement-change.prompt.md |
| Execute inline delta tasks | .github/prompts/delta-action-tasks.prompt.md |
| Keep API docs in sync | .github/prompts/api-contract-maintenance.standard.md |
| Convert docs to markdown | .github/skills/convert-plaintext-to-md/SKILL.md |
| Generate module README/API docs | .github/agents/create-comprehensive-AI-coding-instructions-for-model.agent.md |

## Repository Hygiene for AI Assets

- Keep AI asset names stable and descriptive.
- If a prompt filename changes, update all references immediately.
- Keep examples and standards repository-specific.
- Prefer small, reviewable updates when changing prompts or skills.
