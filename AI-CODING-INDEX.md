# AI Coding Index for DVC

This file is the canonical index of AI coding assets in this repository.

## Goals

- Make AI tooling discoverable and consistent.
- Provide one place to find prompts, skills, agents, and standards.
- Keep implementation behavior aligned with repository conventions.

## Core AI Entry Points

| Type | Path | Purpose |
|---|---|---|
| Global instructions | .github/copilot-instructions.md | Primary coding rules and framework conventions. |
| AI onboarding | AI-README.md | How to run AI workflows in this repository. |
| API contract standard | .github/prompts/api-contract-maintenance.standard.md | Required parity rules for postHandler API actions. |

## Prompts

| Prompt | Path | Use When |
|---|---|---|
| Delta implement change | .github/prompts/delta-implement-change.prompt.md | Implement a prepared delta plan into code. |
| Delta action tasks | .github/prompts/delta-action-tasks.prompt.md | Execute structured delta task blocks in code. |
| Implement simple CRUD | .github/prompts/implement-simple-crud.prompt.md | Build a standard CRUD module from DVC patterns. |
| Implement rich workspace | .github/prompts/implement-rich-workspace.prompt.md | Build workbench-style module flows. |
| Implement authorisation | .github/prompts/implement-authorisation.prompt.md | Add or adjust auth and permission flows. |
| API contract maintenance standard | .github/prompts/api-contract-maintenance.standard.md | Keep API docs synchronized with postHandler. |

## Skills

| Skill | Path | Use When |
|---|---|---|
| Issue to delta | .github/skills/issue-to-delta/SKILL.md | Convert issue text into delta planning documents. |
| Convert plaintext to markdown | .github/skills/convert-plaintext-to-md/SKILL.md | Normalize text docs into markdown. |

## Agents

| Agent | Path | Use When |
|---|---|---|
| Create comprehensive AI coding instructions for model | .github/agents/create-comprehensive-AI-coding-instructions-for-model.agent.md | Generate module architecture docs and API contract docs from source code. |

## Reference Implementations

| Example | Path | Pattern |
|---|---|---|
| Todo | .github/examples/todo/ | Simple CRUD baseline. |
| Contacts | .github/examples/contacts/ | Rich workspace baseline. |

## API Contract Rules (Required)

When module API actions change:

1. Update route/action behavior in code.
2. Update src/app/{module}/{module}.api.md in the same change.
3. Ensure postHandler actions exactly match the API action list.
4. Ensure route examples align with src/controller/{module}.php.
5. Remove stale actions and keep practical curl examples.

Source of truth: .github/prompts/api-contract-maintenance.standard.md

## Maintenance

- Keep this index updated whenever new prompts, skills, or agents are added.
- Prefer adding new AI artifacts under .github/prompts, .github/skills, or .github/agents.
- For renamed prompt files, update references in:
  - .github/skills/issue-to-delta/SKILL.md
  - src/bin/dvc (bootstrap file copy map)
  - This index and AI-README.md
