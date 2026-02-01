# Prompt: Create Rich CRUD Workbench

You are tasked with implementing a **Rich CRUD Workbench** following the DVC Framework standards.

## Context

This pattern is for **domain-centric, workflow-oriented interfaces** where users engage deeply with a single record and its related sub-domains, performing complex tasks in context.

## References

Before proceeding:
1. Review `/app/.github/copilot-instructions.md` section "Controller Patterns: Simple CRUD vs Rich CRUD Workbench"
2. Check root `/app/README.md` for project-specific requirements
3. Check local `README.md` in the module folder (if present) for additional instructions
4. **Study reference implementation**: `.github/examples/contacts/` - Complete Rich CRUD Workbench example
5. Review `.github/examples/README.md` for pattern comparison

## Pattern Overview

**Architecture:**
- Matrix view (feed): discovery interface for finding records (shallow context)
- Workbench view: engagement interface for deep work on a single record
- Tab system: related sub-domains as tabs (invoices, notes, history)
- Accordion toggle: mutual exclusivity between matrix and workbench
- Task-based actions: contextual operations specific to current record

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
    ├── matrix.php    # Contains accordion: feed + workbench
    ├── view.php      # Read-only detail template for workbench tab
    └── edit.php      # Modal form triggered from workbench actions
```

## Implementation Requirements

### 1. Module Configuration (`config.php`)
- Extend root config
- Define `{module}_db_version` constant
- Define `label` constant (used in feed)
- Define `label_view` constant (used in workbench navbar)
- Define `label_edit` constant (used in edit modal)
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
- Include all fields needed for both list and detail views

### 4. DAO (`dao/{entity}.php`)
- Extend framework dao
- Set `$_db_name` and `$template` properties
- Override `Insert()` to add timestamps
- Override `UpdateByID()` to update timestamp
- Implement `getMatrix()` returning dtoSet
- Consider implementing `getRichData(dto)` for additional lookups in detail view

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
- Implement `_index()` rendering matrix view (with accordion)
- Implement `view($id)` loading detail view template **[REQUIRED]**
- Implement `edit($id)` loading edit modal form
- Implement `postHandler()` routing to handler methods

**Critical Controller Addition:**
```php
public function view($id = 0) {
  if ($id = (int)$id) {
    $dao = new dao\{entity};
    if ($dto = $dao->getByID($id)) {
      $this->data = (object)[
        'title' => $this->title = config::label_view,
        'dto' => $dto
      ];
      $this->load('view');  // Loads view.php template
    } else {
      print 'Not Found';
    }
  } else {
    print 'Invalid ID';
  }
}
```

### 7. Views

**index.php** - Sidebar navigation
- Simple HTML with module label
- Links to main view and reports (if applicable)

**matrix.php** - Accordion with feed and workbench
**Critical Structure:**
```php
<div class="accordion" id="<?= $_uidAccordion = strings::rand() ?>">
  <!-- Feed (Matrix) Section -->
  <div class="accordion-item border-0">
    <div id="<?= $_uidAccordion ?>-feed" class="accordion-collapse collapse show" 
         data-bs-parent="#<?= $_uidAccordion ?>">
      
      <!-- Search and Add button -->
      <div class="row g-2 mb-2 d-print-none">
        <div class="col">
          <input type="search" class="form-control" id="<?= $_search ?>">
        </div>
        <div class="col-auto">
          <button class="btn btn-outline-primary" id="<?= $_uidAdd ?>">
            <i class="bi bi-plus-circle"></i> new
          </button>
        </div>
      </div>

      <!-- Data Table -->
      <div class="table-responsive">
        <table class="table table-sm" id="<?= $_table ?>">
          <thead><!-- columns --></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Workbench Section -->
  <div class="accordion-item border-0">
    <div id="<?= $_uidAccordion ?>-workbench" class="accordion-collapse collapse" 
         data-bs-parent="#<?= $_uidAccordion ?>">
      <nav class="navbar navbar-expand d-print-none">
        <div class="navbar-brand">Workbench</div>
        <nav class="navbar-nav ms-auto">
          <button type="button" class="btn-close ms-2" 
                  data-bs-toggle="collapse" 
                  data-bs-target="#<?= $_uidAccordion ?>-feed"></button>
        </nav>
      </nav>
      <!-- Dynamic tabs loaded here via JavaScript -->
    </div>
  </div>

  <script>
    // JavaScript implementation (see below)
  </script>
</div>
```

**JavaScript Requirements for matrix.php:**
```javascript
(_ => {
  const feed = $('#<?= $_uidAccordion ?>-feed');
  const workbench = $('#<?= $_uidAccordion ?>-workbench');
  const table = $('#<?= $_table ?>');
  const search = $('#<?= $_search ?>');

  // CRITICAL: Row click triggers VIEW (not edit)
  .on('click', function(e) {
    e.stopPropagation();
    $(this).trigger('view');  // Opens workbench with tabs
  })

  // Viewer function creates tab system
  const viewer = function(e) {
    const tabs = _.tabs(workbench);      // Initialize tab system
    const view = tabs.newTab('view');     // Create initial tab

    // Load view.php template into tab
    view.pane.on('refresh', e => {
      e.stopPropagation();
      _.fetch.get(_.url(`<?= $this->route ?>/view/${this.dataset.id}`))
        .then(html => view.pane.html(html));
    });

    // Show tab triggers refresh
    view.tab.on('show.bs.tab', e => {
      view.pane
        .html('<h1>Loading...</h1>')
        .trigger('refresh');
    });

    // Add navbar heading
    tabs.nav.prepend(`<h5 class="me-auto mt-2"><?= config::label_view ?></h5>`);

    // Add edit button to navbar
    const btnEdit = $(`<button type="button" class="btn btn-outline-primary ms-2">
      <i class="bi bi-pencil"></i> edit
    </button>`).appendTo(tabs.nav);
    
    btnEdit.on('click', e => {
      _.get.modal(_.url(`<?= $this->route ?>/edit/${this.dataset.id}`))
        .then(m => m.on('success', e => {
          view.tab.trigger('show.bs.tab');  // Refresh view tab
          rowRefresh.call(this, e);          // Update matrix row
        }));
    });

    // Add close button to navbar
    tabs.nav.append(`<button type="button" class="btn-close mt-2 ms-2" 
      data-bs-toggle="collapse" data-bs-target="#<?= $_uidAccordion ?>-feed"></button>`);

    // Open workbench, show initial tab
    workbench.collapse('show');
    view.tab.tab('show');
  };

  // Prevent accordion events from bubbling
  [feed, workbench].forEach(el => {
    el
      .on('hide.bs.collapse', e => e.stopPropagation())
      .on('hidden.bs.collapse', e => e.stopPropagation())
      .on('show.bs.collapse', e => e.stopPropagation())
      .on('shown.bs.collapse', e => e.stopPropagation());
  });

  // Toggle navbar visibility based on active accordion panel
  feed.on('show.bs.collapse', e => $('body').toggleClass('hide-nav-bar', false));
  workbench.on('show.bs.collapse', e => $('body').toggleClass('hide-nav-bar', true));

  _.ready(() => refresh());
})(_brayworth_);
```

**view.php** - Read-only detail template **[REQUIRED]**
```php
<?php
namespace {module};

// $dto is automatically available from $this->data->dto ?>
<div>
  <!-- Display entity fields in read-only format -->
  <div class="row g-2">
    <div class="col-md-3 text-truncate fw-bold">Field Label</div>
    <div class="col mb-2">
      <?= $dto->field_name ?>
    </div>
  </div>
  
  <!-- Repeat for each field -->
</div>
```

**edit.php** - Modal form (same as Simple CRUD)
- Form with unique ID
- Hidden inputs: `action`, `id`
- Input fields bound to `$dto` properties
- Modal structure with header/body/footer
- JavaScript form submit handler
- Trigger 'success' event on save

## Critical Pattern Requirements

### Accordion Structure
- Use Bootstrap accordion with unique ID
- Two accordion items: feed (matrix) and workbench
- `data-bs-parent` attribute for mutual exclusivity
- Feed starts with `collapse show`, workbench with `collapse`

### Row Click Behavior
```javascript
// REQUIRED: Row click triggers VIEW (opens workbench)
.on('click', function(e) {
  e.stopPropagation();
  $(this).trigger('view');  // NOT 'edit'
})
```

### Tab System (_.tabs API)
```javascript
const tabs = _.tabs(containerElement);  // Initialize in workbench element
const tab = tabs.newTab('uniqueId');    // Create new tab
tab.pane                                 // Content pane element
tab.tab                                  // Tab navigation element
tabs.nav                                 // Navbar for custom buttons/controls
```

### Controller Methods
```php
// REQUIRED for Rich CRUD Workbench:
protected function _index()      // Main view with accordion
protected function before()      // Setup and DB check
protected function postHandler() // Route POST actions
public function view($id = 0)    // Detail view template [CRITICAL]
public function edit($id = 0)    // Edit modal

// view() method is REQUIRED - it loads view.php template into tabs
```

## Extending the Workbench

After basic implementation, the workbench can be extended with additional tabs:

### Adding Related Sub-Domain Tabs
```javascript
// Example: Add invoices tab
const invoicesTab = tabs.newTab('invoices');
invoicesTab.pane.on('refresh', e => {
  _.fetch.get(_.url(`invoices/by-contact/${this.dataset.id}`))
    .then(html => invoicesTab.pane.html(html));
});

// Add tab navigation item
invoicesTab.tab.html('Invoices');

// Show tab on demand
invoicesTab.tab.tab('show');
```

### Adding Custom Actions
```javascript
// Example: Add email button to navbar
const btnEmail = $(`<button class="btn btn-outline-secondary ms-2">
  <i class="bi bi-envelope"></i> email
</button>`).appendTo(tabs.nav);

btnEmail.on('click', e => {
  // Trigger email workflow
  _.get.modal(_.url(`${route}/email/${this.dataset.id}`));
});
```

## Validation Checklist

Before completing:
- [ ] Accordion structure with feed and workbench sections
- [ ] Row click opens **workbench** (not edit modal)
- [ ] `view($id)` controller method implemented
- [ ] `view.php` template created with read-only fields
- [ ] `viewer()` JavaScript function creates tab system
- [ ] Initial tab loads `view.php` via AJAX
- [ ] Edit button in workbench navbar triggers modal
- [ ] Modal save refreshes tab and matrix row
- [ ] Close button returns to feed (matrix)
- [ ] Navbar hides when workbench active
- [ ] Context menu still available in matrix
- [ ] All POST actions return proper JSON responses
- [ ] Database schema auto-migrates correctly

## Testing

Test the implementation:
1. Navigate to `/module` - matrix (feed) view loads
2. Click row - workbench opens, matrix collapses, initial tab shows detail view
3. Click edit button in workbench - modal opens with populated data
4. Update fields and save - modal closes, tab refreshes, matrix row updates
5. Click close button - workbench collapses, matrix (feed) shows
6. Right-click row in matrix - context menu with edit/delete still works
7. Search in matrix - filters visible rows
8. Click "new" button - empty edit modal opens

## Common Mistakes to Avoid

❌ **Don't** skip the accordion structure  
❌ **Don't** make row click trigger edit modal directly  
❌ **Don't** forget to implement `view()` controller method  
❌ **Don't** forget to create `view.php` template  
❌ **Don't** forget `data-bs-parent` on accordion items  
✅ **Do** make row click trigger `$(this).trigger('view')`  
✅ **Do** use `_.tabs(workbench)` to create tab system  
✅ **Do** load `view.php` template via AJAX into tab pane  
✅ **Do** add contextual actions to tabs.nav navbar  
✅ **Do** toggle navbar visibility based on accordion state  

## Reference Implementation

Study `.github/examples/contacts/` as the canonical example:
- `.github/examples/contacts/controller.php` - Note the `view($id)` method
- `.github/examples/contacts/views/matrix.php` - Accordion structure and viewer function
- `.github/examples/contacts/views/view.php` - Read-only detail template
- `.github/examples/contacts/views/edit.php` - Modal form pattern
- `.github/examples/contacts/dao/` - Complete DAO/DTO/schema implementation

## When to Use This Pattern

Use Rich CRUD Workbench when:
- Entity has complex relationships (invoices, notes, history, attachments)
- Users need to work on one record for extended periods
- Context switching between related data is common
- Business processes require multiple views/actions on same record
- Task-based workflows are needed (not just field updates)

Examples: contacts, customers, projects, orders, cases, tickets

---

**Remember**: The workbench is **not just a "read" view**—it's a place where business work happens. Tabs represent related sub-domains, and actions are task-based, not field-based. This pattern scales better than pure CRUD for complex domain models.
