---
description: "Implement an approved DVC module delta that changes application code"
---

# Implement Delta Change

Implement the approved change described by the selected delta file in this DVC repository. This prompt is for changes to application source code; it is not a planning or documentation-only workflow.

Shared standard: `.github/prompts/api-contract-maintenance.standard.md`

## Agent Mode Gate

If the active agent is **Agent Delta**, return exactly the following sentence and stop. Do not inspect files, search the repository, invoke tools, or make edits:

> The prompt changes code. Change your agent mode to remove the Markdown-only restriction.

## Scope Discipline

- Implement only the selected delta and its acceptance criteria.
- Do not create a new delta file while implementing an existing one.
- If the delta is already `IMPLEMENTED` and contains unchecked `## Delta` items, implement those items in the same file.
- Preserve existing user changes and unrelated work.
- If requirements conflict with the delta, stop and ask for confirmation before changing scope.

## 1. Establish Context

Read the selected delta completely, including `## Open Questions`, `## Assumptions`, `## Decisions`, `## Validation Plan`, and `## Readiness`.

Before editing, read:

- The applicable `README.md` in the target folder, if present. In `.github/`, use `AI-README.md` as the governing document.
- The workspace `README.md` when setup, server, or test conventions matter.
- `.github/copilot-instructions.md` for DVC architecture and coding standards.
- The target module's `config.php`, `controller.php`, `handler.php`, DAO/DTO files, schema files, and views as applicable.
- `src/controller/{module}.php` for route mapping.
- `src/app/{module}/{module}.api.md` when the module exposes POST actions, if present.

Resolve the local behavior before editing: identify the code that directly controls the requested behavior, state one falsifiable implementation hypothesis, and choose the cheapest focused check that could disconfirm it.

Do not claim readiness if the delta has unresolved blocking questions or is not marked ready for implementation.

## 2. Implement the Delta

Follow existing module patterns and keep the smallest coherent change.

### Database and data layer

When schema changes are required:

- Update `src/app/{module}/config.php` with the next database version.
- Update `dao/db/{entity}.php` using declarative `$dbc->defineField()` and `$dbc->check()` patterns.
- Update `dao/dto/{entity}.php` with matching typed-default properties.
- Update `dao/{entity}.php` only when custom queries or persistence behavior are needed.
- Keep raw SQL out of controllers and handlers. Escape DAO query values with framework-supported APIs.
- Preserve the repository convention of empty strings or zero values for cleared fields where applicable.

### Controllers and handlers

- Keep controllers focused on lifecycle hooks, GET routing, view preparation, and POST dispatch.
- Call `parent::before()` and register module view paths according to existing patterns.
- Route new POST actions through `postHandler()` and retain the parent fallback.
- Keep business logic and all data access in handlers and DAOs, respectively.
- Cast IDs and validate required input. Return the framework's `json::ack()` / `json::nak()` responses consistently.
- Add authorization checks early when the delta requires restricted behavior.

### Views and JavaScript

- Follow the module's established view style and Bootstrap version.
- Use unique IDs and the `_brayworth_` IIFE pattern where applicable.
- For `_.get.modal()` content, use one root element, normally a `<form>`, keep the inline script inside it, and let the framework show the modal.
- Use `modal.trigger('success')` after a successful save so the owning view can refresh.
- Escape rendered user data and sanitize dynamic HTML according to framework conventions.

### Documentation and API contracts

When `postHandler()` actions are added, removed, renamed, or changed:

- Update `src/app/{module}/{module}.api.md` in the same change.
- Keep documented actions, request fields, response payloads, route names, and practical curl examples in parity with code.
- Update module `README.md` when the feature changes documented module behavior or usage.

## 3. Validate Progressively

After the first substantive edit, run the narrowest executable check available before more exploration or patching. Prefer:

1. A focused behavior test or existing test for the touched module.
2. A narrow PHP syntax, lint, or type check for changed PHP files.
3. A focused API request or browser check for changed routes and UI.
4. A repository test suite when the change has broader impact.

For database changes, verify the migration/version path and resulting fields. For POST changes, compare every `postHandler()` action with the API contract. For UI changes, verify load, submit, success refresh, error handling, and console errors where possible.

Do not widen the change merely to fix unrelated failures. Report unrelated pre-existing failures separately.

## 4. Finalize the Delta

After implementation and validation, update the same delta file:

- Set `## Readiness` to `Status: IMPLEMENTED` and replace its reason with a brief implementation summary.
- Add a dated implementation entry under `## Decisions` (or `## Log` if the delta has that section), including the validation result.
- Add `## Release Notes` immediately after `## Readiness` and before `## Delta`. Keep it end-user focused and under 50 words.
- Add `## Delta` after `## Release Notes` if it does not exist. Preserve existing history and use checkboxes for follow-up work.
- On reruns, implement unchecked follow-up items, mark completed items `- [x]`, and add a dated implementation entry.
- Do not discard existing decisions, release notes, or delta history.

## Completion Checklist

- [ ] Delta requirements and acceptance criteria implemented
- [ ] Applicable README and framework conventions followed
- [ ] Database schema, DTO, DAO, and version updated when required
- [ ] Controller and handler routing updated when required
- [ ] Views and JavaScript updated when required
- [ ] Authorization and input validation added when required
- [ ] API contract updated when POST actions changed
- [ ] Focused executable validation completed
- [ ] Delta marked `IMPLEMENTED` with dated implementation notes
- [ ] End-user release notes and follow-up `## Delta` section maintained

## Common Mistakes to Avoid

- Do not write raw SQL in controllers or handlers.
- Do not forget database version increments for schema changes.
- Do not add a POST action without updating its API contract.
- Do not bypass `parent::postHandler()` for unknown actions.
- Do not use deprecated global aliases when a framework namespace is available.
- Do not call `modal.modal('show')` inside HTML fetched through `_.get.modal()`.
- Do not leave an implemented delta marked ready or discard its follow-up history.
