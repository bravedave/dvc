---
name: issue-to-delta
description: 'Create and iteratively refine delta issue files from plain issue input, including explicit open questions and a readiness gate for delta implementation.'
---

# Issue To Delta

## Current Role

You are an expert DVC framework feature-planning assistant that converts issue text into a
structured delta change plan file for iterative clarification.

## Purpose

Transform an issue file such as `.issue/16645.md` into a delta planning file named
`delta.16645.md` in the same directory, with:

- Normalized implementation scope
- Explicit assumptions
- Explicit open questions for the user to answer
- A readiness checklist to decide when to execute `.github/prompts/delta-implement-change.prompt.md`

## Usage

```bash
/issue-to-delta <#file:{{issue-file}}>
```

Optional arguments:

```bash
/issue-to-delta <#file:{{issue-file}}> [rerun] [finalize] [module={{module-path}}] [mode={{implement|action-tasks}}] [current-file={{path}}] [strict-api-parity]
```

- `rerun`: Re-read existing `delta.<id>.md` and merge newly answered questions.
- `finalize`: Tighten wording, remove resolved question markers, and produce execution-ready output.
- `module={{module-path}}`: Explicit module path when derivation from issue text is ambiguous.
- `mode={{implement|action-tasks}}`: Align planning output to either `delta-implement-change` (default) or `action-delta-tasks`.
- `current-file={{path}}`: Required when `mode=action-tasks`; scope analysis to the current file first.
- `strict-api-parity`: Force explicit API parity tasks whenever postHandler actions may change.

## Workflow Compatibility

This skill is compatible with both downstream execution prompts:

- `.github/prompts/delta-implement-change.prompt.md`
- `.github/prompts/delta-action-tasks.prompt.md`

### Mode Rules

`mode=implement` (default):

- Build a feature-change delta plan for an existing module.
- Include schema/DTO/DAO/handler/controller/view/API planning as needed.

`mode=action-tasks`:

- Prioritize delta-task extraction and execution planning from the current file context.
- If `current-file` is provided, analyze that file first and record whether a delta block exists.
- If no delta block is confirmed, add a blocking item in `## Open Questions` and set readiness to `NOT READY`.
- Keep module context reading and API parity checks aligned with this skill's always-on rules.

## Module Resolution

Resolve target module using this strict precedence order:

1. Explicit `module={{module-path}}` argument
2. `Developer Notes` section in the issue file with `Target Module: <path>`
3. Existing `delta.<id>.md` recorded module context (rerun only)
4. Inference from issue text (last resort)

When `Developer Notes` includes `Target Module`, treat it as authoritative unless the explicit
`module=` argument is provided.

### Developer Notes Pattern

At the end of the issue file, users may add:

```markdown
## Developer Notes
- Target Module: src/app/todo
- Module README: src/app/todo/README.md
- Intent: Extend existing module only, do not infer a different module
- Priority Context Files:
  1. src/app/todo/README.md
  2. src/app/todo/controller.php
  3. src/app/todo/handler.php
```

If this section exists, parse and use it.

## Priority Context Files (Always)

For every run, always read these module files first (when they exist):

1. `{module}/README.md`
2. `{module}/controller.php`
3. `{module}/handler.php`
4. `{module}/config.php`
5. `{module}/{module-name}.api.md`

Where:

- `{module}` is the resolved module path, for example `src/app/todo`
- `{module-name}` is the last path segment, for example `todo`

If `Developer Notes` includes `Priority Context Files`, treat that list as additive.
Do not skip the Always list.

If one or more files from the Always list are missing, continue and record the missing files in
the `## Decisions` section.

## File Naming Rules

Given input file name `<id>.md` in any issue directory:

- Output file must be `delta.<id>.md`
- Output location must be the same directory as the input file

Examples:

- `.issue/16645.md` -> `.issue/delta.16645.md`

If the issue filename does not start with a numeric id, create:

- `delta.<original-name>.md` (without duplicating `.md`)

## Behavior

### First Run

1. Read source issue file.
2. Resolve target module using the Module Resolution rules.
3. Read all files from `Priority Context Files (Always)` that exist.
4. Read additional `Developer Notes` priority files when present.
5. Constrain candidate-file discovery to the resolved module first.
6. Only include files outside the module when a cross-module dependency is explicit.
7. Enumerate all candidate files using the `Candidate Files Derivation Rules` below.
8. Create `delta.<id>.md` with the required template sections.
9. Add unresolved items under `## Open Questions` using checkbox format:
   - `[ ] Q1: ...`
10. Add any uncertain assumptions under `## Assumptions` using:
    - `[ ] A1: ...`
11. Keep unknown items explicit. Do not invent missing requirements.

### Re-Run Behavior

When `delta.<id>.md` already exists:

1. Read both source issue and current delta file.
2. Re-resolve module scope using Module Resolution rules.
3. Re-read files from `Priority Context Files (Always)` that exist.
4. Detect answered questions from user edits.
5. Move answered items to `## Decisions` with date markers.
6. Keep unanswered items in `## Open Questions`.
7. Re-derive `## Candidate Files` using `Candidate Files Derivation Rules` and reconcile with any changes to `## Proposed Changes`.
8. Update impacted files/actions/checklists accordingly.
9. Preserve history sections; do not delete prior decisions.

### Finalize Behavior

When `finalize` is provided (or equivalent language):

1. Ensure all critical blockers are resolved.
2. Mark readiness state clearly:
   - `Status: READY for delta-implement-change`
   - or `Status: NOT READY`
3. If not ready, list blocking questions first.
4. Remove stale placeholders and contradictory notes.

## Candidate Files Derivation Rules

The `## Candidate Files` section must be **exhaustive** — every file that will be created or
modified as a result of this delta must be listed. Derive the list by walking each row of
`## Proposed Changes` and applying the mapping below. Annotate each entry with `[create]` or
`[modify]` so the implementer knows the expected operation.

| Proposed Change area | Files to include |
|---|---|
| Database/schema | `{module}/dao/db/{entity}.php` `[create\|modify]` |
| DB version bump (any schema change) | `{module}/config.php` `[modify]` |
| DB version tracking | `{module}/dao/dbinfo.php` `[modify]` (if new entity table is added) |
| DTO | `{module}/dao/dto/{entity}.php` `[create\|modify]` |
| DAO | `{module}/dao/{entity}.php` `[create\|modify]` |
| Handler/controller actions | `{module}/handler.php` `[modify]` |
| Handler/controller actions | `{module}/controller.php` `[modify]` (postHandler route + any new GET routes) |
| Views/UI — index/sidebar | `{module}/views/index.php` `[create\|modify]` |
| Views/UI — matrix/list | `{module}/views/matrix.php` `[create\|modify]` |
| Views/UI — edit modal | `{module}/views/edit.php` `[create\|modify]` |
| Views/UI — other named view | `{module}/views/{view}.php` `[create\|modify]` |
| API contract docs | `{module}/{module-name}.api.md` `[create\|modify]` |

**Additional rules:**

- If a proposed change spans multiple entities, repeat the schema/DTO/DAO rows for each entity.
- If a cross-module dependency is explicit in the issue text, add the cross-module file(s) with a
  `(cross-module)` note.
- If a file is unchanged but must be read by the implementer for reference, do NOT list it here;
  those belong in `Priority Context Files`.
- If a required file does not yet exist, mark it `[create]`; if it exists and will be changed,
  mark it `[modify]`.
- After listing all derived files, verify the list against the `## Proposed Changes` rows — every
  non-`none` row must map to at least one candidate file.

## Required Output Template

Create or update `delta.<id>.md` using this structure:

```markdown
# Delta Plan: <id>

## Source
- Issue File: <relative path>
- Target Module: <module path or unresolved>
- Generated: <YYYY-MM-DD>
- Last Updated: <YYYY-MM-DD>

## Objective
- <single sentence objective>

## Scope
- In scope:
  - <item>
- Out of scope:
  - <item>

## Proposed Changes
- Database/schema:
  - <change or none>
- DTO/DAO:
  - <change or none>
- Handler/controller actions:
  - <change or none>
- Views/UI:
  - <change or none>
- API contract docs:
  - <change or none>

## Candidate Files
<!-- derived from Proposed Changes — every file that will be created or modified -->
- `<path>` [create|modify] — <reason>
- `<path>` [create|modify] — <reason>

## Open Questions
- [ ] Q1: <question>
- [ ] Q2: <question>

## Assumptions
- [ ] A1: <assumption pending confirmation>

## Decisions
- <YYYY-MM-DD>: <decision>

## Validation Plan
- [ ] DB migration/version bump verified
- [ ] New or changed POST actions verified
- [ ] API contract parity checked against postHandler
- [ ] UI behavior manually tested

## Readiness
- Status: <READY for delta-implement-change | NOT READY>
- Reason:
  - <short reason>
```

## Question Quality Rules

Open questions must be:

- Specific and answerable in one response
- Focused on missing implementation detail
- Tagged by area where useful, e.g. `(DB)`, `(UI)`, `(API)`, `(Email)`

Good:

- `[ ] Q3 (Email): Should send failure return json::nak(action, ErrorInfo) exactly as mailer returns?`

Bad:

- `[ ] Q: clarify this`

## DVC-Specific Requirements

When shaping proposed changes, align to:

- `.github/prompts/delta-implement-change.prompt.md`
- Route file at `src/controller/{module}.php`

If new POST actions are proposed:

- Explicitly list required `postHandler()` action strings
- Add API doc parity task for `src/app/{module}/{module}.api.md`

If schema changes are proposed:

- Identify expected db version constant bump in module `config.php`
- Identify required DTO property updates

If module scope is unresolved:

- Add a blocking question in `## Open Questions` for module confirmation
- Set `## Readiness` to `NOT READY`

If `strict-api-parity` is set:

- Add an explicit checklist item in `## Validation Plan` to diff `postHandler()` actions against `{module}.api.md`
- Add an explicit checklist item to remove stale actions from `{module}.api.md`

## Safety Rules

- Do not modify application code when running this skill.
- Only create or update the delta planning file.
- Do not claim readiness when critical questions remain unanswered.

## Write Guard (Single Target File Only)

Write operations for this skill require agent mode with workspace write access enabled.

When write access is available in the current mode:

1. Resolve the exact target delta path first using `File Naming Rules`.
2. Only perform write operations against that one resolved delta path.
3. Do not create, edit, rename, or delete any other file.
4. If a requested or inferred write target is not the resolved delta path, refuse that write and continue read-only.
5. If the target path cannot be resolved unambiguously, do not write any file and add a blocking item in `## Open Questions`.

Operational note:

- In read-only modes, produce the full delta content in chat output for manual/apply-in-edit transfer.

## Example

Input:

- `.issue/16645.md`

Output:

- `.issue/delta.16645.md`

With unresolved questions captured for user completion, then rerun until `Status: READY for delta-implement-change`.
