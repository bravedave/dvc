files here are ports from bravedave/esse

- page.php: Bootstrap-oriented page lifecycle helper.
- tailwind.php: Tailwind-oriented page lifecycle helper (navbar, aside/main content row, footer).

## Bootstrap 6 Renderer

`render6.php` is an opt-in page shell. Controllers supply closures and retain
responsibility for view lookup, Markdown conversion and data preparation:

```php
$renderer = new \bravedave\dvc\esse\render6;
$renderer([
	'title' => $this->title,
	'navbar' => fn() => $this->load('navbar6'),
	'aside' => fn() => $this->load('aside6'),
	'main' => fn() => $this->load('main6'),
	'footer' => fn() => $this->load('footer6'),
]);
```

Slots must be closures that output content. Navbar and footer closures supply
their own semantic elements. Only an empty main region is rendered by default.
Other options are `breadcrumb` (breadcrumb objects), `bodyClass`, `css`,
`scripts`, `late` and `meta`. CSS/script entries accept URLs or trusted raw tags;
URL shorthand is escaped. Raw tags, meta and late entries are trusted developer
markup, not request input. Script URLs produce classic scripts; modules require
an explicit `<script type="module">` tag. Breadcrumb URLs are trusted application
URLs; their attributes and labels are escaped, but schemes are not validated.

The renderer loads jQuery, Bootstrap 6 CSS and ES-module JavaScript, and Bootstrap
Icons. Import components with `import { Collapse } from '@bootstrap'`; no global
`bootstrap`, `_brayworth_` or `dopo` is installed. Import maps precede modules.
Existing Bootstrap 5 rendering and version globals are unchanged.

Layout rules live alongside the renderer in `render6.css`, registered through
`cssmin::esseFiles(6)` and served at `assets/esse/6`. The renderer loads that
stylesheet automatically and emits no inline layout CSS. The versioned bundle
includes only `esse.markdown.css` and `render6.css`; legacy `assets/esse` retains
its original file list. Each bundle has a separate cache. Legacy root font,
print sizing, dropdown and fixed-height layout overrides are not loaded by BS6.
The rules fill the viewport with independently scrollable aside/main regions.
The aside is hidden below 768px; the caller must provide access when it contains
required content. The BS6 demo navbar uses `Collapse` on that same aside, including
expanded state and Escape handling. Print expands main content and hides aside.
Default BS5 views, legacy helpers, theme variants and Toast UI are not provided.

The demo at `/bs6` loads Markdown through the controller. Sidebar Markdown and
table alignment rules come from `esse.markdown.css`, without duplicate copies.
Legacy `brayworth.markdown.css` typography is not included in the BS6 bundle.
The demo no longer needs a separate CSS file or a `bs6css()` action.
Run the isolated renderer checks from the repository root:

```sh
php tests/app/bs6/Render6Test.php
```
