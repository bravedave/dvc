# Amazing

This is an experiment with Preact

I think I'm liking preact

The **bias of React/Preact toward interoperability with a server-side PHP framework like [`bravedave/dvc`](https://github.com/bravedave/dvc)** can be broken down in terms of *philosophical compatibility* and *technical impedance*. Here's the real talk:

---

### ✅ **Pros / Interoperability Biases in Favor**

These make Preact (more than React) a *natural fit* for your server-side setup:

#### 1. **Modular & Fragmented DOM Updates**

* `dvc` already supports late-served fragments and dynamic content areas.
* Preact’s small, self-contained components fit perfectly into that model (load-on-demand UI without SPA overhead).
* You can mount Preact to divs inside late-served partials—zero routing conflict.

#### 2. **No Build Required (If You Want)**

* Preact + `htm` (tagged templates) means **you can write JSX-like components without Babel or Webpack**.
* That aligns with your `js/` folder convention—just serve `.js` files directly.
* This keeps your dev pipeline thin and simple.

#### 3. **No Server Coupling**

* Preact doesn’t care what backend is serving the HTML.
* Your PHP templates can just inject data using `<script type="application/json">` or window vars for hydration.
* No need to refactor your controller/view structure.

#### 4. **Scoped Enhancements ("Islands")**

* You can use Preact **only** where it adds value—e.g. navbar state, modals, dynamic tables—without touching the rest of the page.
* This fits your “load a little, don’t reload everything” principle.
* In other words: **Preact doesn't try to own the page**, just a node.

#### 5. **Small Footprint**

* Preact’s \~4kB gzipped size won’t bloat your delivery.
* With `dvc`, that’s a huge deal—you’re already optimizing for snappy loads and small deltas.

---

### ❌ **Cons / Interop Friction**

These are areas where Preact/React fundamentally *diverge* from the `dvc` server-side flow:

#### 1. **State vs Session Disconnect**

* Server-side PHP relies on session or user objects across requests.
* Preact/React maintain in-memory state on the client.
* You’ll need glue logic (e.g. `BroadcastChannel`, custom sync, or API calls) to reflect session/user changes across reloads and tabs.

#### 2. **Lack of Out-of-the-Box Server Integration**

* Unlike frameworks like Next.js or Inertia, Preact doesn’t natively bind to PHP’s server/render cycle.
* You must manually coordinate data fetches and insert dynamic content into the frontend.

#### 3. **Client-side Interop with jQuery**

* If you’re still using jQuery for DOM manipulation, timing conflicts (e.g. jQuery empties or moves a node after Preact renders it) can break components unless you’re careful.
* You’ve already noticed this with `empty()` vs `render(null)`.

#### 4. **No Routing Harmony**

* If you ever want to use frontend routing, you’ll need to **opt in deliberately** and avoid colliding with PHP paths.
* This is mostly a non-issue if you use Preact only for fragments, but worth noting.

---

### 🔄 **Design Alignment**

Your current philosophy:

| Principle                 | `dvc` | Preact                   |
| ------------------------- | ----- | ------------------------ |
| Late serving              | ✅     | ✅                        |
| Stateless server views    | ✅     | ✅                        |
| No build pipelines        | ✅     | ✅ (`htm`)                |
| jQuery interop            | ✅     | ⚠️ with caution          |
| Per-component enhancement | ✅     | ✅                        |
| Full SPA control          | ❌     | ✅ (optional, not forced) |

---

### ✅ Verdict

**Preact plays *very* well with `bravedave/dvc` as long as:**

* You treat Preact as a DOM enhancer, not a page owner.
* You avoid state/routing bloat unless needed.
* You handle shared state across windows with lightweight sync (`BroadcastChannel`, API fetch).
* You don't expect it to "know" PHP — you feed it data just like jQuery would.

---

Want a sample where your PHP view injects JSON data into a `<script>` tag, and Preact reads it on boot?
