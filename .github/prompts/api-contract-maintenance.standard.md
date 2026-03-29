# API Contract Maintenance Standard

Use this standard whenever a module exposes POST API actions through `postHandler()`.

## Scope

- Applies to modules under `src/app/{module}/`
- Applies when actions are added, removed, renamed, or behavior/inputs/responses change

## Required API Contract File

- Maintain `src/app/{module}/{module}.api.md`
- If absent and API actions exist, create it

## Minimum Contract Content

- Access URL and route source (`src/controller/{module}.php`)
- Authentication assumptions (development vs normal auth)
- How to call endpoints (`POST` with `action`)
- Action reference list generated from `postHandler()`
- Practical `curl` examples for real actions
- Notes on response shape differences (`ack/nak` envelope vs direct payload)

## Parity Rules

- Every action in `postHandler()` must appear in `{module}.api.md`
- No action may remain in `{module}.api.md` if removed from `postHandler()`
- Example URLs must match the route class name in `src/controller/{module}.php`
- Example request fields must match handler input expectations

## Change Management

- Update API docs in the same change as endpoint contract changes
- Do not leave placeholder task text in API docs
- Keep docs production-ready after each merged change

## Suggested Verification

- Diff `postHandler()` actions vs API action list and resolve mismatches
- Run at least one `curl` example against the module route
