# Render 6 - Bootstrap 6 Framework

## Scope

Create a single page to be the next rendering evolution point for the dvc framework. It should abstract the use of the self closing `src/bravedave/dvc/esse/page.php`. It should provide a similar level of functionality to the renderBS5 method, with exception that javascript will need to move to a modules

# Candidate files

- any file in the `tests/app/bs6` path
- `src/bravedave/dvc/esse/render6.php` (create)


## Structure

+----------------------------------+
|              NavBar              |
+-------+--------------------------+
| aside | [breadcrumbs]            |
|       | main content             |
+-------+--------------------------+
|              Footer              |
+----------------------------------+

## Acceptance Criteria

1. The page is rendered with a footer that sits at the bottom, a navbar that sits at the top. The aside and main sections should be elastic in nature to fill the space.
2. @bootstrap is a working ES-module import
3. Do load
   - jQuery
   - Boostrap JS, CSS and Icons
4. Do not load
   - _brayworth_, dopo
5. Rendering will be against the controller

## Aritifacts

- `src/bravedave/dvc/esse/render6-000.php`
- `src/bravedave/dvc/esse/try1/*`

## Work to Date

- Try1 : Too complex, includes page loading - that should be left to the controller

## Implementation

- `render6.php` supplies the page shell; controller closures own view loading.
- jQuery, Bootstrap 6 CSS/module JS and icons load without `_brayworth_` or dopo.
- The BS6 demo main is Markdown with lorem ipsum and a table.
- The navbar imports `Collapse` from `@bootstrap` to reveal the mobile aside.
- `render6.css` sits beside the renderer and is registered in `cssmin::esseFiles(6)`.
- The renderer loads `assets/esse/6`, isolated from legacy global styles with a separate cache.
- `esse.markdown.css` is reused without duplication; legacy Markdown typography is excluded.
- The footer remains at the viewport bottom; aside/main scroll independently.
- This is opt-in, not full BS5 helper/default-view parity. See `README.md` for options.

Validation: 18 isolated renderer checks; PHP lint; live Markdown/CSS HTTP checks;
desktop/mobile browser geometry, module interaction, resize and print checks.
Browser validation reported no page errors, failed requests or legacy requests.