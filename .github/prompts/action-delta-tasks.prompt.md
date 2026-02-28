# Action Delta Tasks

This prompt guides you through finding and implementing **delta** task comments embedded in the codebase.

A delta comment is a structured TODO block left by a developer that describes a set of tasks to complete. They look like this:

```php
/**
 * delta
 * <context description of the current state>
 *
 * task
 *  1. <specific change required>
 *  2. <specific change required>
 *  3. <specific change required>
 */
```

Once all tasks are implemented, the delta comment block is **replaced** with a concise `// delta implemented` comment (or removed entirely if the surrounding code makes it redundant).

---

## Step 1 – Locate the Delta

If you were not given a specific file, search for delta comments across the codebase:

```bash
grep -rn "* delta" src/ --include="*.php"
```

Read the full delta block carefully:
- What is the **context** (what already exists / what state we are in)?
- What are the numbered **tasks**?
- Which file is the delta in, and what method/function surrounds it?

---

## Step 2 – Read the Module Context

Before writing any code, gather context:

1. **Framework rules** – re-read `.github/copilot-instructions.md` for patterns
2. **Module README** – read `src/app/{module}/README.md` if it exists
3. **config.php** – note the current `{module}_db_version`
4. **handler.php** – understand existing handler methods and patterns
5. **controller.php** – review existing routes and `postHandler()` match arms
6. **DAO files** – understand the current data layer (`dao/`, `dao/dto/`, `dao/db/`)
7. **Views** – understand how data is rendered and what JavaScript already exists

Do not guess at patterns — always read the actual files first.

---

## Step 3 – Plan the Implementation

For each numbered task in the delta, decide:

| Task | Type | Files to change |
|------|------|-----------------|
| Add a database field | Schema + DTO + DB version | `dao/db/{entity}.php`, `dao/dto/{entity}.php`, `config.php` |
| Update a DAO query | DAO method | `dao/{entity}.php` |
| Add handler logic | Handler method | `handler.php` |
| Add a new POST action | Handler + controller routing | `handler.php`, `controller.php` |
| Update a view | PHP view + JS | `views/*.php` |
| Update a GET route | Controller | `controller.php` |

Write out the plan before touching any files. If tasks have dependencies (e.g. a field must exist before a query can use it), implement them in dependency order.

---

## Step 4 – Implement Each Task

Work through the tasks one at a time, following DVC framework conventions.

### Database field additions

**Schema file (`dao/db/{entity}.php`):**
```php
$dbc->defineField('group_sent_with_authority_id', 'int', null, null, 0);
$dbc->defineIndex('idx_group_sent', 'group_sent_with_authority_id');
$dbc->check();
```

**DTO (`dao/dto/{entity}.php`):**
```php
public $group_sent_with_authority_id = 0;
```

**Increment version in `config.php`:**
```php
const {module}_db_version = 2;  // incremented for new field
```

### DAO method additions

Follow existing patterns in the same DAO. Use `sprintf` with `%d` for integers and `$this->quote()` for strings:

```php
public function getByStatus(string $status): array {
  $sql = sprintf(
    'SELECT * FROM `%s` WHERE `status` = %s ORDER BY `updated` DESC',
    $this->_db_name,
    $this->quote($status)
  );
  return (new dtoSet)($sql);
}

public function updateStatusByIds(array $ids, string $status): void {
  if (empty($ids)) return;
  $idList = implode(',', array_map('intval', $ids));
  $this->db()->exec(
    sprintf(
      'UPDATE `%s` SET `status` = %s, `updated` = %s WHERE `id` IN (%s)',
      $this->_db_name,
      $this->quote($status),
      $this->quote(self::dbTimeStamp()),
      $idList
    )
  );
}
```

### Handler logic additions

Handler methods must be `public static` and return `json`. Type-cast all user input:

```php
public static function entityUpdateStatus(ServerRequest $request): json {
  $action = $request('action');
  $id = (int)$request('id');
  $status = trim($request('status'));

  if (!$id || empty($status)) {
    return json::nak($action);
  }

  (new dao\{entity})->UpdateByID(['status' => $status], $id);
  return json::ack($action);
}
```

### View / JavaScript additions

Follow existing patterns in the module's views. Use `strings::rand()` for unique element IDs. Refer to `.github/copilot-instructions.md` for the full list of framework JS utilities (`_.url()`, `_.fetch.post()`, `_.get.modal()`, `_.growl()`, `_.context()`, etc.).

If the delta requires exposing new data via the matrix response, update both the handler's `getMatrix()` (or equivalent DAO method) **and** the JavaScript `matrix()` render function in the matrix view.

---

## Step 5 – Replace the Delta Comment

Once **all tasks** are implemented, replace the delta comment block with a one-line note:

```php
// delta implemented: {new_field} field, {newDaoMethod} DAO method, {new-action} handler action
```

The comment should briefly name what was done so a future reader understands the history.

Do **not** leave the original delta block in place — it should only exist while the work is pending.

---

## Step 6 – Update the Module README

If the module has a `README.md`, update it to reflect the changes made:

- Add new fields to any data model documentation
- Document new DAO methods
- Document new handler actions
- Update the changelog or "recent changes" section if one exists

If no README exists and the changes are significant, create one using the module's `config::label` as the title.

---

## Step 7 – Verify

Run through this checklist before considering the delta complete:

### Database
- [ ] All new fields defined in schema file (`dao/db/*.php`)
- [ ] Schema file ends with `$dbc->check()`
- [ ] DTO updated with matching public properties
- [ ] `{module}_db_version` incremented in `src/app/{module}/config.php`

### Data layer
- [ ] New DAO methods follow existing patterns (timestamps, quoting, etc.)
- [ ] No raw SQL in handlers or controllers — all via DAO

### Handler / Controller
- [ ] Handler methods are `public static` returning `json`
- [ ] All user input is type-cast
- [ ] `postHandler()` match expression updated if new actions added

### Views / JavaScript
- [ ] New columns/fields displayed where required
- [ ] No JavaScript console errors
- [ ] `_.growl(d)` used for responses
- [ ] `success` event triggered on modal save where applicable

### Delta comment
- [ ] Original delta block removed / replaced with `// delta implemented: ...`

### Documentation
- [ ] Module README updated (or created) if changes are significant

---

## Common Mistakes to Avoid

❌ **Don't** leave the delta comment in place after implementing
❌ **Don't** implement only some tasks — action all numbered items
❌ **Don't** forget to increment the DB version when adding fields
❌ **Don't** write raw SQL in handlers — create DAO methods
❌ **Don't** guess module patterns — read the actual files first
❌ **Don't** forget to update the DTO when adding a DB field

✅ **Do** read the surrounding code for context before implementing
✅ **Do** follow the exact patterns already used in the module
✅ **Do** replace the delta comment with a brief summary line
✅ **Do** update the README if the change is user-visible or architecturally significant
✅ **Do** test that the DB migration runs cleanly (increment version)

---

## Reference

- **Framework patterns**: `.github/copilot-instructions.md`
- **General feature additions**: `.github/prompts/implement-idea-module-change.prompt.md`
- **Refactoring pattern**: `.github/prompts/refactor-controller-to-handler.prompt.md`
- **Simple CRUD example**: `.github/examples/todo/`
- **Rich CRUD Workbench example**: `.github/examples/contacts/`
