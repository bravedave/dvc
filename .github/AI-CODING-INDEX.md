# AI Coding Instructions - Master Index

Canonical index of AI-assisted development assets for the DVC framework repository.

## Main Documentation

### [Copilot Instructions](copilot-instructions.md)
Primary framework guide for architecture, module patterns, controllers, handlers, DAOs, DTOs, views, security, testing, and workflow standards.

### [README](README.md)
Practical workflow guide for planning, implementing, and documenting changes with AI in this repository.

### [API Contract Maintenance Standard](prompts/api-contract-maintenance.standard.md)
Required API parity rules when `postHandler()` actions change.

## Prompt Library

### [Delta Implement Change](prompts/delta-implement-change.prompt.md)
Use to implement a prepared delta plan into code.

### [Delta Action Tasks](prompts/delta-action-tasks.prompt.md)
Use to execute structured delta task blocks embedded in code.

### [Implement Simple CRUD](prompts/implement-simple-crud.prompt.md)
Use to scaffold or extend a standard CRUD module using DVC patterns.

### [Implement Rich Workspace](prompts/implement-rich-workspace.prompt.md)
Use to build workbench-style modules with richer record workflows.

### [Implement Authorisation](prompts/implement-authorisation.prompt.md)
Use to add or adjust authorization and permission workflows.

## Skills

### [Issue to Delta](skills/issue-to-delta/SKILL.md)
Create and iteratively refine `delta.<issue-id>.md` files from issue input.

### [Convert Plaintext to Markdown](skills/convert-plaintext-to-md/SKILL.md)
Convert plain text documents to markdown using a consistent structure.

## Agent

### [Create Comprehensive AI Coding Instructions For Model](agents/create-comprehensive-AI-coding-instructions-for-model.agent.md)
Analyze module source and produce high-quality module documentation and API contract docs.

## Reference Implementations

### [Simple CRUD Example](examples)
Authoritative baseline for standard CRUD module structure and behavior.

### [Rich CRUD Workbench Example](examples/contacts)
Authoritative baseline for a two-context feed/workbench interaction model.

## Required API Parity Rules

When module actions change:

1. Update route and action behavior in code.
2. Update `src/app/{module}/{module}.api.md` in the same change.
3. Ensure `postHandler()` actions exactly match API action lists.
4. Keep request/response field names in sync with handler behavior.
5. Remove stale actions and examples.

Source of truth: [prompts/api-contract-maintenance.standard.md](prompts/api-contract-maintenance.standard.md)

## Maintenance Rules

- Folder governance convention: `README.md` in any folder is the authoritative document for that folder and should always be read for contextual governance before making changes.
- Keep this index current when prompts, skills, agents, or examples change.
- Prefer placing AI assets under `.github/prompts`, `.github/skills`, and `.github/agents`.
- If any prompt/skill filename changes, update all references in:
  - [README.md](README.md)
  - [copilot-instructions.md](copilot-instructions.md)
  - This index
