# DVC Framework - Reference Implementation Examples

This directory contains canonical reference implementations for DVC Framework module patterns.

## Purpose

These examples serve as **authoritative references** for AI agents and developers when creating new modules. They demonstrate:

- Complete file structure and organization
- Controller patterns and routing
- Handler business logic and POST processing
- DAO/DTO implementation with database schema
- View templates and JavaScript patterns
- Best practices and naming conventions

## Available Examples

### 1. **Todo Module** - Simple CRUD Pattern

**Location**: `.github/examples/todo/`

**Pattern**: Data-centric, maintenance-focused interface

**Use When:**
- Entity has few fields (3-10)
- No complex relationships or sub-domains
- Users perform quick maintenance tasks
- Operations are simple field updates

**Key Characteristics:**
- Matrix view: table listing with search
- Modal edit: inline form overlay for create/update
- Row click → edit modal
- Stateless operations

**Files Structure:**
```
todo/
├── config.php           # Module configuration
├── controller.php       # Routes GET/POST requests
├── handler.php          # Business logic for POST actions
├── dao/
│   ├── dbinfo.php      # Database version tracking
│   ├── todo.php        # Data Access Object
│   ├── dto/
│   │   └── todo.php    # Data Transfer Object
│   └── db/
│       └── todo.php    # Database schema definition
└── views/
    ├── index.php       # Sidebar navigation
    ├── matrix.php      # Main data table
    └── edit.php        # Modal form
```

**Reference in prompts**: See `.github/implement-simple-crud.prompt.md`

---

### 2. **Contacts Module** - Rich CRUD Workbench Pattern

**Location**: `.github/examples/contacts/`

**Pattern**: Domain-centric, workflow-oriented interface

**Use When:**
- Entity has complex relationships (invoices, notes, history, attachments)
- Users spend extended time working on a single record
- Context switching between related data is common
- Business tasks require multiple views/actions

**Key Characteristics:**
- Matrix view (feed): discovery interface for finding records
- Workbench view: deep engagement with single record
- Tab system: related sub-domains as tabs
- Accordion toggle: switch between discovery and engagement
- Task-based actions: contextual operations

**Files Structure:**
```
contacts/
├── config.php           # Module configuration (with label_view, label_edit)
├── controller.php       # Routes including view($id) method
├── handler.php          # Business logic for POST actions
├── dao/
│   ├── dbinfo.php      # Database version tracking
│   ├── contacts.php    # Data Access Object
│   ├── dto/
│   │   └── contacts.php # Data Transfer Object
│   └── db/
│       └── contacts.php # Database schema definition
└── views/
    ├── index.php       # Sidebar navigation
    ├── matrix.php      # Accordion: feed + workbench
    ├── view.php        # Read-only detail template (loaded in tabs)
    └── edit.php        # Modal form
```

**Reference in prompts**: See `.github/implement-rich-workspace.prompt.md`

---

## Usage for AI Agents

When generating new modules:

1. **Identify pattern** - Determine if Simple CRUD or Rich CRUD Workbench is appropriate
2. **Review example** - Study the corresponding example implementation
3. **Copy structure** - Use the same file organization and naming
4. **Adapt code** - Modify for entity-specific fields and business logic
5. **Preserve patterns** - Maintain JavaScript patterns, controller methods, and view structures

### Critical Differences

| Feature | Simple CRUD (todo) | Rich CRUD Workbench (contacts) |
|---------|-------------------|--------------------------------|
| **Row Click** | `trigger('edit')` → modal | `trigger('view')` → workbench |
| **Matrix View** | Always visible | Collapses when workbench opens |
| **Controller** | `_index()`, `edit()` | `_index()`, `view()`, `edit()` |
| **Views** | matrix.php, edit.php | matrix.php, view.php, edit.php |
| **UI Pattern** | Table + modal | Accordion (feed ↔ workbench) |
| **Tabs** | Not used | Dynamic tabs via `_.tabs()` |

## Maintenance

These examples are:
- **Git-tracked** - Version controlled with project
- **Documented** - Referenced in copilot-instructions.md
- **Stable** - Should only change for pattern improvements
- **Canonical** - Source of truth for module patterns

When updating DVC Framework patterns, update these examples to reflect current best practices.

## Related Documentation

- **Module Development Guide**: `.github/copilot-instructions.md`
- **Simple CRUD Prompt**: `.github/implement-simple-crud.prompt.md`
- **Rich CRUD Workbench Prompt**: `.github/implement-rich-workspace.prompt.md`
- **Framework Standards**: `vendor/bravedave/dvc/STANDARDS.md`

---

**Note**: These are reference implementations only. They demonstrate patterns and structure but are not meant to be run directly from this location. When implementing a new module, create it in `src/app/{module-name}/` following these examples.
