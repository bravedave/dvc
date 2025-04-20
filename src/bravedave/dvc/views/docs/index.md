# **Introducing DVC: A Lightweight PHP Framework for Modern Web Development**

If you're a PHP developer looking for a **simple, fast, and intuitive** way to build web applications without the bloat of massive frameworks, meet **DVC** (Dave’s View Controller).

DVC is a **lightweight PHP framework** designed for developers who want:
✅ **Minimal setup** – No complex configurations
✅ **Clear MVC structure** – Without unnecessary abstractions
✅ **SQLite-first** – Perfect for rapid development
✅ **PSR-compliant** – Where it matters (PSR-4, PSR-7-inspired)

Let’s dive into what makes DVC special.

---

## **Why DVC?**

### **1. Built for Speed (and Simplicity)**

Unlike monolithic frameworks, DVC strips away the excess and focuses on:

- **Convention over configuration** – Sensible defaults, no endless YAML files
- **Zero bloat** –minimal dependencies slowing you down
- **Pure PHP** – No complex templating languages, just PHP & Markdown

### **2. SQLite as a First-Class Citizen**

DVC embraces SQLite for **lightning-fast development**:

1. Set the db_type to sqlite
2. Run the query

```php
$users = (new bravedave\dvc\dtoSet)("SELECT * FROM users");
```

No need for MySQL/MariaDB unless you scale—perfect for prototypes, APIs, and small-to-medium apps.

### **3. Clean MVC Without the Overhead**

- **Controllers** handle logic
- **Views** are plain PHP or Markdown
- **Models** via lightweight DAO/DTO patterns

Example controller:

```php
class products extends \bravedave\dvc\controller {
  protected function _index() {
    $this->data = ['products' => (new ProductDAO)->getDtoSet()];
    $this->load('products.php');
  }
}
```

### **4. Modern HTTP Handling (PSR-7 Inspired)**

DVC’s `ServerRequest` simplifies HTTP interactions:

```php
$request = new \bravedave\dvc\ServerRequest();
$id = $request->getQueryParam('id'); // GET param
$name = $request('name'); // POST param
```

---

## **Getting Started in 60 Seconds**

### **1. Install via Composer**

```bash
composer require bravedave/dvc
vendor/bin/dvc make::application
```

### **2. Create Your First Module**

```bash
vendor/bin/dvc make::module blog
```

This generates:

- `src/controller/blog.php` (Controller)
- `src/app/blog/views/` (Templates)

### **3. Run the Dev Server**

```bash
php src/app/application.php
```

Visit `http://localhost:8000/blog` – you’re live!

---

## **Who Is DVC For?**

✔ **PHP devs who miss classic ASP’s simplicity**
✔ **Developers tired of framework bloat**
✔ **Startups/prototypes needing SQLite speed**
✔ **Anyone who prefers PHP over templating engines**

---

## **What’s Next?**

- **Explore the Docs**: [GitHub Repository](https://github.com/bravedave/dvc)
- **Try the Tutorial**: Build a blog in 10 minutes
- **Contribute**: DVC is open-source!

---

### **Final Thought**

DVC isn’t trying to be Laravel or Symfony—it’s **a lightweight alternative for developers who want control without complexity**.

Give it a spin and let me know what you think! 🚀

**🔗 GitHub**: [github.com/bravedave/dvc](https://github.com/bravedave/dvc)
