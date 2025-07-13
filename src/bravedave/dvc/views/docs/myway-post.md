# My Way - Post Handling

[Docs](.) | [My Way](myway.md) | **Post Handling**

---

## Post Handling

**Purpose:** This page framework's POST handling mechanisms and how they facilitate CRUD operations.

### Framework POST Architecture

The framework provides two primary methods for handling POST requests, both working together to facilitate CRUD operations:

#### 1. Form-Based POST \(`_brayworth_.fetch.post.form`)

**Usage:** Posts form data from HTML forms
**Input:** FormData object from HTML form elements
**Routing:** Data is posted to the controller itself (not to specific controller methods)

**Example Implementation:**

```javascript
(_ => {
  const form = $('#form-id');
  const modal = $('#modal-id');

  modal.on('shown.bs.modal', () => {
    form.on('submit', function(e) {
      _.fetch.post.form(_.url('route-name'), this).then(d => {
        if ('ack' == d.response) {
          modal.trigger('success');
          modal.modal('hide');
        } else {
          _.growl(d);
        }
      });
      return false;
    });
  });
})(_brayworth_);
```

**Key Characteristics:**

* Posts to the controller's main endpoint (exception: rarely posts to specific controller methods)
* Uses FormData from HTML form elements
* Automatically handles form serialization
* Ideal for standard CRUD operations with form inputs

#### 2. JSON-Based POST (`_brayworth_.fetch.post`)

**Usage:** Posts JSON data for programmatic operations
**Input:** JavaScript object with action and data properties
**Routing:** Same routing mechanism as form-based POST

**Example Implementation:**

```javascript
(_ => {
  _.fetch.post(_.url('route-name'), {
    action: 'some-action',
    moredata: 'additional-data',
    id: recordId
  }).then(d => {
    if ('ack' == d.response) {
      // Handle success
    } else {
      _.growl(d);
    }
  });
})(_brayworth_);
```

**Key Characteristics:**

* Posts JSON objects directly
* Requires explicit action specification
* Suitable for AJAX operations and programmatic data submission
* Same routing and handling as form-based POST

### POST Request Routing

**Controller Level:**

* All POST requests are routed to the controller's `postHandler` method
* The `postHandler` uses `bravedave\dvc\ServerRequest` to process POST variables
* Actions are extracted and routed to appropriate handler methods

**Handler Level:**

* POST data is further routed to handler classes in the same namespace
* Handler classes contain the business logic for CRUD operations
* Actions determine which specific handler method is called

### CRUD Operation Flow

1. **Form Submission:** User submits form or JavaScript triggers POST
2. **Data Serialization:** Framework serializes form data or JSON object
3. **Controller Routing:** `postHandler` receives and processes the request
4. **Action Extraction:** Action parameter determines operation type
5. **Handler Routing:** Request routed to appropriate handler method
6. **Business Logic:** Handler executes CRUD operation
7. **Response:** Framework returns standardized response format

### Response Format

**Success Response:**

```json
{
  "response": "ack",
  "data": {...}
}
```

**Negative Response:**

```json
{
  "response": "nak",
  "message": "Error description"
}
```

### Integration with CRUD Operations

Both POST methods work together to provide complete CRUD functionality:

* **Create:** Form-based POST for new record creation
* **Read:** Typically handled via GET requests, but can use JSON POST for complex queries
* **Update:** Form-based POST for editing existing records
* **Delete:** JSON-based POST for programmatic deletion operations

**Example CRUD Actions:**

```javascript
// Create/Update
_.fetch.post.form(_.url('user-subscriptions'), formElement)

// Delete
_.fetch.post(_.url('user-subscriptions'), {
  action: 'delete',
  id: recordId
})

// Custom operations
_.fetch.post(_.url('user-subscriptions'), {
  action: 'bulk-update',
  ids: [1, 2, 3],
  status: 'active'
})
```

### Form Action Variable

Forms typically include an action variable that specifies the operation:

```html
<form action="<?= $this->route ?>" method="post">
  <input type="hidden" name="action" value="save">
  <!-- form fields -->
</form>
```

The action variable helps the framework route the request to the appropriate handler method and determine the type of CRUD operation to perform.
