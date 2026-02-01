# Prompt: Create Simple CRUD Controller

You are tasked with implementing a **Simple CRUD Controller** following the DVC Framework standards.

## Context

This pattern is for **data-centric, maintenance-focused interfaces** where users perform straightforward Create, Read, Update, Delete operations on simple entities.

## References

Before proceeding:
1. Review `/app/.github/copilot-instructions.md` section "Controller Patterns: Simple CRUD vs Rich CRUD Workbench"
2. Check root `/app/README.md` for project-specific requirements
3. Check local `README.md` in the module folder (if present) for additional instructions
4. **Study reference implementation**: `.github/examples/todo/` - Complete Simple CRUD example
5. Review `.github/examples/README.md` for pattern comparison

## Pattern Overview

**Architecture:**
- Matrix view: table listing with search
- Modal edit: inline form overlay for create/update
- Direct actions: click row → edit, context menu → delete
- Stateless operations

**Files Required:**
```
src/app/{module}/
├── config.php
├── controller.php
├── handler.php
├── dao/
│   ├── dbinfo.php
│   ├── {entity}.php
│   ├── dto/{entity}.php
│   └── db/{entity}.php
└── views/
    ├── index.php
    ├── matrix.php
    └── edit.php
```

## Implementation Requirements

### 1. Module Configuration (`config.php`)
- Extend root config
- Define `{module}_db_version` constant
- Define `label` constant
- Implement `{module}_checkdatabase()` static method

### 2. Database Schema (`dao/db/{entity}.php`)
- Define standard fields: `created`, `updated`
- Define entity-specific fields with appropriate types
- Add performance indexes
- Include `$dbc->check()` at end

### 3. DTO (`dao/dto/{entity}.php`)
- Extend framework dto
- Public properties with typed defaults
- Match database schema exactly

### 4. DAO (`dao/{entity}.php`)
- Extend framework dao
- Set `$_db_name` and `$template` properties
- Override `Insert()` to add timestamps
- Override `UpdateByID()` to update timestamp
- Implement `getMatrix()` returning dtoSet

### 5. Handler (`handler.php`)
- Final class with static methods
- Type cast all user input
- Use DAO for all data operations
- Return `json::ack()` or `json::nak()`
- Implement methods:
  - `{entity}Save(ServerRequest $request): json`
  - `{entity}Delete(ServerRequest $request): json`
  - `{entity}GetByID(ServerRequest $request): json`
  - `{entity}GetMatrix(ServerRequest $request): json`

### 6. Controller (`controller.php`)
- Extend dvc controller
- Implement `before()` with database check
- Implement `_index()` rendering matrix view
- Implement `edit($id)` loading edit form
- Implement `postHandler()` routing to handler methods

### 7. Views

**index.php** - Sidebar navigation
- Simple HTML with module label
- Links to main view and reports (if applicable)

**matrix.php** - Main data table
- Search input with unique ID
- Add button
- Table with entity-specific columns
- JavaScript with IIFE pattern:
  - `getMatrix()` - fetch data via POST action 'get-matrix'
  - `matrix(data)` - render table rows
  - `edit()` - **trigger edit modal on row click**
  - `rowDelete()` - delete confirmation and action
  - `contextmenu()` - right-click menu
  - `refresh()` - reload data
  - `_.ready(() => refresh())` - initialize

**edit.php** - Modal form
- Form with unique ID
- Hidden inputs: `action`, `id`
- Input fields bound to `$dto` properties
- Modal structure with header/body/footer
- JavaScript form submit handler
- Trigger 'success' event on save

## Critical Pattern Requirements

### Matrix View Row Click Behavior
```javascript
// REQUIRED: Row click triggers EDIT modal (not view)
.on('click', function(e) {
  e.stopPropagation();
  $(this).trigger('edit');  // Opens edit modal
})

// Edit handler
const edit = function() {
  _.get.modal(_.url(`<?= $this->route ?>/edit/${this.dataset.id}`))
    .then(m => m.on('success', e => $(this).trigger('refresh')));
};
```

### Controller Methods
```php
// ONLY these methods needed for Simple CRUD:
protected function _index()      // Main view
protected function before()      // Setup and DB check
protected function postHandler() // Route POST actions
public function edit($id = 0)    // Edit modal

// DO NOT implement view() method - not used in Simple CRUD
```

### No Accordion Structure
Simple CRUD does **not** use accordion or workbench patterns. Matrix view is always visible.

## Validation Checklist

Before completing:
- [ ] Row click opens **edit modal** (not workbench)
- [ ] No `view()` controller method
- [ ] No `view.php` template
- [ ] No accordion structure in matrix.php
- [ ] Context menu includes edit and delete
- [ ] All POST actions return proper JSON responses
- [ ] Database schema auto-migrates correctly
- [ ] Search filter works on all visible columns
- [ ] Modal closes and table refreshes on save

## Testing

Test the implementation:
1. Navigate to `/module` - matrix view loads
2. Click row - edit modal opens with populated data
3. Update fields and save - modal closes, row updates
4. Right-click row - context menu with edit/delete
5. Click delete - confirmation, then row removed
6. Search - filters visible rows
7. Click "new" button - empty edit modal opens

## Common Mistakes to Avoid

❌ **Don't** implement workbench/tab system for Simple CRUD  
❌ **Don't** create `view()` controller method or `view.php` template  
❌ **Don't** use accordion structure  
❌ **Don't** make row click trigger `$(this).trigger('view')`  
✅ **Do** make row click trigger `$(this).trigger('edit')`  
✅ **Do** use modal for all editing  
✅ **Do** keep matrix always visible  

## Reference Implementation

Study `.github/examples/todo/` as the canonical example:
- `.github/examples/todo/controller.php` - Controller pattern
- `.github/examples/todo/handler.php` - Handler pattern
- `.github/examples/todo/views/matrix.php` - Matrix with edit-on-click
- `.github/examples/todo/views/edit.php` - Modal form pattern
- `.github/examples/todo/dao/` - Complete DAO/DTO/schema implementation

---

**Remember**: Simple CRUD is for **quick data maintenance**. If the entity needs workflow context, related sub-domains, or extended engagement, use the Rich CRUD Workbench pattern instead.
