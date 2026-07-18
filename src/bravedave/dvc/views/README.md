# Views

This folder contains shared framework view fragments used by controllers and page builders.

## Navbar Variants

### navbar-default.php

File: [src/bravedave/dvc/views/navbar-default.php](src/bravedave/dvc/views/navbar-default.php)

Notes:
- Bootstrap-oriented navbar fragment used by existing Bootstrap page layouts.
- Supports optional aside toggle for small screens using `show-aside` on `<body>`.
- Uses `menu.json` (when present) to render a dropdown app menu.
- Falls back to title as either a link (`$pageUrl`) or plain text.
- Renders standard right-side links: Home, docs, GitHub.
- Relies on Bootstrap classes and Bootstrap JS collapse/dropdown behavior.

### navbar-tailwind.php

File: [src/bravedave/dvc/views/navbar-tailwind.php](src/bravedave/dvc/views/navbar-tailwind.php)

Notes:
- Tailwind-native equivalent of the default navbar for Tailwind page flows.
- Preserves the same high-level behavior as the Bootstrap variant:
  - optional aside toggle on mobile
  - optional menu dropdown sourced from `menu.json`
  - title and page URL fallback logic
  - Home, docs, GitHub links
- Uses utility classes and lightweight inline JavaScript for mobile nav toggle.
- Targets Tailwind content roles (`content-secondary`, `content-primary`) when handling aside/main mobile visibility.

## Footer Variants

### footer.php

File: [src/bravedave/dvc/views/footer.php](src/bravedave/dvc/views/footer.php)

Notes:
- Bootstrap-oriented footer fragment.
- Includes its own outer `<footer>` wrapper and Bootstrap grid classes.

### footer-tailwind.php

File: [src/bravedave/dvc/views/footer-tailwind.php](src/bravedave/dvc/views/footer-tailwind.php)

Notes:
- Tailwind footer content fragment for Tailwind page rendering.
- Includes its own outer `<footer>` element and Tailwind classes.
- Compatibility note: behaves like `footer.php` where the view renders the footer wrapper.

## Usage Guidance

- Use [src/bravedave/dvc/views/navbar-default.php](src/bravedave/dvc/views/navbar-default.php) with Bootstrap page rendering.
- Use [src/bravedave/dvc/views/navbar-tailwind.php](src/bravedave/dvc/views/navbar-tailwind.php) with Tailwind page rendering.
- Use [src/bravedave/dvc/views/footer.php](src/bravedave/dvc/views/footer.php) with Bootstrap page rendering.
- Use [src/bravedave/dvc/views/footer-tailwind.php](src/bravedave/dvc/views/footer-tailwind.php) with Tailwind page rendering; the view renders its own footer wrapper for compatibility.
- Keep behavior parity between both navbar files when adding links or menu logic so user navigation stays consistent across themes.
