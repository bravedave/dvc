# DVC - Ad Hoc Response

## Custom Response Handlers

Want to break from convention? No problem. Here’s how you’d roll your own **barebones response handlers**—because flexibility matters, even if `json::ack` and `renderBS5` are how *I* prefer to do it.

### 1. DIY JSON Response

Skip `json::ack/nak` and manually send JSON:

```php
public function customJson(bool $success, string $message, mixed $data = null): never {
    header('Content-Type: application/json');
    echo json_encode([
        'custom_status' => $success ? 'yep' : 'nope',
        'msg' => $message,
        'payload' => $data
    ]);
    exit; // Terminate
}

// Usage:
$this->customJson(true, 'Task deleted', ['id' => 123]);
```

---

### 2. Ad-Hoc HTML Rendering

Bypass `renderBS5` and output HTML directly:

```php
public function rawHtml(string $title, callable $body): never {
    header('Content-Type: text/html');
    echo "<!DOCTYPE html>";
    echo "<html><head><title>$title</title></head>";
    echo "<body>";
    $body(); // Execute arbitrary HTML logic
    echo "</body></html>";
    exit;
}

// Usage:
$this->rawHtml('My Page', function() {
    echo "<h1>No Bootstrap here!</h1>";
    echo "<p>Wild custom HTML appears.</p>";
});
```

---

### Why This Works (But I Don’t Recommend It)

- **Pros**: Total control.
- **Cons**:
  - No standardized structure (e.g., `ack/nak` consistency).
  - Manual header management.
  - Harder to maintain.

---

### The Takeaway

Use `json::ack` and `renderBS5` for **consistency**—or go rogue when needed. Your call. 🔧

*(But seriously, stick to the conventions. They’re there for a reason.)*
