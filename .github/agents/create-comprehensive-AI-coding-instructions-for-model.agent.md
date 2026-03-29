# Create Module README Agent

> **Recommended model:** Claude Sonnet 4.6 or higher — this agent performs multi-file analysis that benefits from larger context windows and stronger code reasoning.

## Goal

Analyze a DVC Framework module (anywhere in the workspace) and generate a comprehensive `README.md` documenting its architecture for AI models to understand, modify, and extend the module.

When the module exposes POST API actions via `postHandler()`, also create or update `{module-dir}/{module-name}.api.md` as the module's API contract.

## Input

The user provides either:
- A module name (e.g., `todo`) — assumed to be at `src/app/{module}/`
- A full relative path to the module directory (e.g., `src/app/todo`)

Always resolve to an absolute workspace path before proceeding.

## Output Rules

- Max 1000 words in the generated README.md
- Exactly 6 sections (see Output Sections below)
- Each section: max 5 bullets or table rows per sub-topic
- No paragraphs longer than 2 lines
- Use tables for field/method/action listings
- Use code blocks sparingly — only for non-obvious patterns
- Save to `{module-dir}/README.md` where `{module-dir}` is the resolved module path
- If `postHandler()` exists, save/update `{module-dir}/{module-name}.api.md`
- Keep API docs action-complete: every routed action must be listed

## Steps

### Step 1: Discover Files

Resolve the module directory from the user's input. If a short name like `todo` is given, check `src/app/todo/` first; if a relative path is given, resolve it to its absolute workspace path.

List all files in the resolved module directory recursively. Note which standard module files exist and which are absent:

- `config.php`, `controller.php`, `handler.php`
- `dao/dbinfo.php`, `dao/*.php`, `dao/dto/*.php`, `dao/db/*.php`
- `views/*.php`

### Step 2: Read Configuration

Read `config.php`. Extract:

- Namespace and parent class
- Database version constant name and value
- Label constant
- Database check method name

### Step 3: Read Controller

Read `controller.php`. Extract:

- All GET route methods (public methods and `_index()`)
- All POST actions from `postHandler()` match expression
- `before()` hook contents
- View paths registered
- Data prepared in `$this->data` for each route

Also check `src/controller/{module}.php` to confirm the URL route mapping so API examples can use the correct URL path.

### Step 4: Read Handler

Read `handler.php`. Extract:

- Every static method name, its corresponding action string, and return type
- Input fields read from `$request()` per method
- DAO classes instantiated and methods called
- Validation logic (type casts, empty checks, permission checks)

### Step 5: Read Data Layer

Read all files in `dao/`, `dao/dto/`, and `dao/db/`. Extract:

- **DAO**: table name (`$_db_name`), DTO template (`$template`), custom methods with SQL
- **DTO**: all public properties with types (inferred from defaults) and default values
- **Schema**: field definitions (`defineField` calls), indexes (`defineIndex` calls)
- Cross-check: every DTO property should map to a schema field, and vice versa

### Step 6: Read Views

Read all files in `views/`. Extract:

- **index.php**: navigation links, sidebar content
- **matrix.php**: table columns, search input, action buttons, JS actions posted, context menu items, refresh mechanism
- **edit.php**: form fields and their DTO bindings, hidden inputs (action, id), modal behavior, submit handler

### Step 7: Cross-Reference and Verify

Before writing output, verify consistency:

- Every action in `postHandler()` has a matching handler method
- Every handler method's DAO calls reference existing DAO classes/methods
- Every view JS `action` value appears in `postHandler()`
- Every form field name in `edit.php` is read by the corresponding handler method
- Every DTO property has a matching schema field
- Flag any inconsistencies as notes in the README

If API actions are present, also verify:

- Every action in `postHandler()` exists in `{module}.api.md`
- Every action documented in `{module}.api.md` exists in `postHandler()`
- Every example URL matches the route class name in `src/controller/{module}.php`

### Step 8: Generate README.md

Target path: `{module-dir}/README.md`

**Check if the file exists first.** If it exists, overwrite it entirely using `replace_string_in_file` (match the old full content) or `run_in_terminal` with a heredoc redirect. Do **not** use `create_file` on an existing file — it will fail.

Write the file using the 6-section structure below. Keep within the 1000-word limit. Prioritize accuracy over completeness — use actual names, fields, and SQL from the code.

### Step 9: Generate or Update API Contract

Target path: `{module-dir}/{module-name}.api.md` where `{module-name}` is the leaf directory name.

**Check if the file exists first.** Use `create_file` for new files. For existing files, overwrite using `run_in_terminal` with a heredoc redirect or replace the full content via edit tool.

If the module has `postHandler()` actions, write or update the file with:

- Accessing the application (base URL and module route)
- How to call endpoints (`POST`, `action` field)
- Action reference generated from `postHandler()`
- At least 2 practical `curl` examples for real actions
- Notes about response envelope differences (`json::ack/json::nak` vs direct payload)

Do not leave placeholder tasks in API docs. Output production-ready documentation.

## Output Sections

### Section 1: Module Overview

```markdown
# {Module Display Name} Module

**Namespace:** `{module}`
**Table(s):** `{table_names}`
**Purpose:** {One-line description}
```

- What the module manages
- Primary entities and relationships
- Key business functions (max 3 bullets)

### Section 2: Data Model

For each entity, a table:

| Field | Type | Default | Purpose |
|-------|------|---------|---------|

- Include indexes
- Note relationships to other modules/tables

### Section 3: Routes & Actions

**GET Routes** — table mapping URL pattern to controller method and description.

**POST Actions** — table mapping action string to handler method, input fields, and DAO operation.

### Section 4: Data Access Layer

For each DAO:

- Table name, DTO template
- Custom methods (name, purpose, return type)
- Override behavior (e.g., timestamps on Insert/Update)

For each DTO:

- List properties only if they differ from schema fields (e.g., computed/enriched fields)

### Section 5: Views & UI

For each view file, bullet list:

- **matrix.php**: columns displayed, search behavior, row click action, context menu items, add button behavior
- **edit.php**: form fields, validation, modal events
- **index.php**: navigation structure

### Section 6: Data Flow

Three compact flows (use `→` notation, no code blocks):

- **Page Load**: GET → controller → views → JS `_.ready()` → POST `get-matrix` → render table
- **Save Record**: form submit → POST action → handler → DAO → `json::ack` → modal close → refresh
- **Delete Record**: context menu → confirm → POST delete → handler → DAO → refresh

Note any non-standard flows specific to this module.

## Quality Checks

Before finishing, verify the generated README:

- [ ] Word count ≤ 1000
- [ ] Exactly 6 sections
- [ ] All field names, method names, action strings match actual code
- [ ] No placeholder text like `{entity}` or `{module}` remains
- [ ] Tables are properly formatted markdown
- [ ] Inconsistencies from Step 7 are noted
- [ ] If `postHandler()` exists, `{module}.api.md` is created/updated
- [ ] API action list exactly matches current `postHandler()` actions
- [ ] API examples use the correct route from `src/controller/{module}.php`
