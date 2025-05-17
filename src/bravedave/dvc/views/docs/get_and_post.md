# DVC - Get & Post

## Web Development in the DVC Framework: A GET/POST Paradigm

### Introduction

The **DVC framework** provides a flexible foundation for web development, prioritizing simplicity without imposing rigid conventions. While the framework itself doesn’t enforce specific architectural patterns, this documentation outlines a **deliberate, structured approach** to streamline API-driven interactions—a methodology refined through practical experience and inspired by the reliability of early communication protocols (like the **ack/nak** handshake from X-modem and military systems).

At its core, this paradigm revolves around **two pillars**:

1. **GET** for safe, idempotent data retrieval.
2. **POST** for state-modifying operations.

By adhering to these conventions, the framework achieves consistency, scalability, and maintainability—while leaving room for adaptation. Below, we’ll focus on the **POST workflow**, where structured payloads, handler-driven logic, and unambiguous feedback mechanisms converge to create a robust API layer.

### 1. GET Requests

- **Purpose**: Retrieve data or load resources (e.g., rendering views, fetching data).
- **Convention**:
  - Targets class functions in controllers via URL parameters.
  - Example: `https://example.com/controller/method?param=value` invokes `method()` in the controller.
  - Used for non-destructive, idempotent operations.

### 2. POST Requests

- **Purpose**: Submit data to modify server state (e.g., create/update records).

---

#### DVC’s POST Workflow

##### Client-Side

1. **Payload Structure**:
   - Include an `action` key to define the server-side logic branch.
   - Example:

     ```javascript
     const payload = {
       action: 'todo-update', // Defines the handler
       id: 123,
       description: 'New task'
     };
     ```

2. **API Call**:
   - Use `_.api()` to POST data to the server (e.g., `_.api(_.url('people'), payload)`).
   - Expects a JSON response (see [Response Structure](get_and_post#response)).

---

##### Server-Side (Controller)

1. **Routing**:
   - POST data is funneled to the `postHandler` method in the controller.
   - The `action` parameter determines the logic branch.

2. **Handler Logic**:
   - Uses PHP’s `match()` to map `action` to specific handlers.
   - Example (simplified):

     ```php
     public function postHandler() {

       $action = $this->getPost('action');

       match ($action) {
         'todo-update' => handler::TodoUpdate(),  // and you would write a class to do this
         default => json::nak('Invalid action')
       };
     }
     ```

---

### 3. API Response Convention

Responses follow a standardized JSON structure:

```json
{
  "response": "ack" | "nak", // Success/failure indicator
  "description": "Verbose status message",
  "data": { ... } // Optional structured data (e.g., updated records)
}
```

- **`ack`**: Acknowledgment of success (e.g., `200 OK`).
- **`nak`**: Negative acknowledgment (e.g., validation errors, server-side failures).
- **Military/Comms Analogy**: Borrows from X-modem/CRC protocols for unambiguous feedback.

---

### Why This Convention?

1. **Consistency**: All API interactions follow the same `action`-driven pattern.
2. **Scalability**: Easily extend handlers by adding new `action` cases.
3. **Debugging**: Clear `ack`/`nak` responses simplify client-side error handling.
4. **Separation of Concerns**:
   - Controllers delegate logic to handlers.
   - Views interact with APIs via standardized payloads.

---

### Example Flow

1. Client POSTs `{ action: 'todo-update', ... }` to `/todo`.
2. Controller’s `postHandler` routes to `handler::TodoUpdate()`.
3. Server processes, returns:

   ```json
   {
     "response": "ack",
     "description": "Todo item #123 updated",
     "data": { "id": 123, "description": "New task" }
   }
   ```

4. Client’s `_.api()` handles success/error via `.then()`/`.catch()`.

---

This paradigm ensures a clean, maintainable API layer while embracing your nostalgic nod to early comms protocols. 🚀

### Key Takeaway

The DVC framework abstracts web interactions into **action-driven POST workflows**, aligning with modern API-first patterns. By enforcing `action` as the routing pivot and leveraging `match`, it simplifies backend logic while enabling frontend clients to behave like API consumers.
