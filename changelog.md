# Changelog

All notable changes to Chassis will be documented in this file.

## [0.0.5] - 11.06.2026

### Changed

- The Menu button now sits at the bottom left on phones by default, when `chassis.json` has no `menu.toggle`. Set `"menu": { "toggle": "top-right" }` for the old place, or use the new `.app-nav--top` class.
- Settings in `design` now win over the `menu.toggle` presets.

### Added

- `menu.design` in `chassis.json` sets how the Menu button looks: `background`, `text`, `border`, and `padding`. These become `--menu-toggle-background`, `--menu-toggle-color`, `--menu-toggle-border`, and `--menu-toggle-padding`, and win over the look that comes with `menu.toggle`.
- `.app-nav--top` for themes without `chassis.json`.

## [0.0.4] - 11.06.2026

### Added

- `menu.toggle` in `chassis.json` sets where the Menu button sits on phones: `top-right` (the default), `top-left`, `bottom-left`, or `bottom-right`.
- The build keeps `dist/` from each pull request as a download for 7 days.

### Changed

- The phone menu's position comes from custom properties (`--menu-toggle-*`, `--menu-panel-*`). `.app-nav--bottom` works as before.

## [0.0.3] - 11.06.2026

### Added

- `chassis.json`: a theme sets its colors, fonts, spacing, and widths in one file, with no Sass. `styles()` prints them after the stylesheet. A child theme's file overrides its parent's. See [Set the design with `chassis.json`](readme.md#set-the-design-with-chassisjson).
- `schemas/chassis.schema.json`, for autocomplete and typo checks in editors.
- `designFiles()`, `design()`, and `designCss()` on `Chassis`.

### Fixed

- `Providers\Backdrop` now loads the CSS, the ClassicPress add-on, and the JavaScript, as the docs said it did.

## [0.0.2] - 11.05.2026

### Fixed

- `.social-menu` now works with text links as well as icons, for Novaris's `$engine->social()`.

## [0.0.1] - 11.05.2026

The first release. Chassis is the shared structure for Novaris and ClassicPress themes: it owns how things are built, and each theme decides how they look.

### Turning Chassis on

- The service provider is what turns Chassis on. Without it, the CSS and JavaScript don't load.
- `Providers\Novaris` copies the built files to `public/assets/chassis`, and themes print them with `styles()` and `scripts()`. It logs a warning if it can't copy them, and removes old copies.
- `Providers\Backdrop` loads the CSS, the ClassicPress add-on, and the JavaScript itself.
- `platform()` returns `novaris`, `backdrop`, or `plain`, so the registered provider decides the platform.
- `button` and `badge` template tags on Novaris.
- `Techkodeninja\Chassis\Chassis` with `badge()`, `button()`, `classes()`, `path()`, `scssPath()`, `jsPath()`, and `distPath()` helpers.

### Design

Themes change Chassis with CSS custom properties, which load after it.

- Colors grouped by where they appear: Global, Header, Content, Widget, and Footer. Widget colors follow the content colors unless a theme sets them.
- Settings for spacing, widths, fonts, heading sizes, corners, shadows, box borders, header and menu padding, content padding, the sidebar gap, buttons, badges, pagination, and post boxes.

### Styles

SCSS in numbered ITCSS layers, with BEM class names.

- **Settings and tools:** breakpoints, colors, fonts, sizes, and spacing, plus `up()`, `down()`, `color()`, `space()`, and `width()`, with build errors for unknown names.
- **Elements:** a reset, body text, headings, links, tables, forms, inline code, code blocks, and quotes.
- **Objects:** `.container`, `.section`, `.grid`, and `.gallery`.
- **Layouts:** `.app`, `.app-header` with `--full`, `.app-nav` with `--bottom`, `.app-content` with `--sidebar-left` and `--sidebar-right`, `.app-main`, `.app-sidebar`, and `.app-footer`.
- **Components:** `.card`, `.badge`, `.button`, `.section-header`, `.pagination`, `.widget`, `.social-menu`, `.entry__*`, and `.code-copy`.
- **Utilities:** `.screen-reader-text`, `.skip-link`, editor alignments, and text and visibility helpers.
- **ClassicPress add-on:** captions, sticky posts, threaded comments, the comment form, the search form, the calendar widget, `the_posts_pagination()`, `entry-*` classes, and the `[gallery]` shortcode.

### JavaScript

- `js/app-nav.js`: the mobile menu, with `aria-expanded`, Escape, click-outside, and resize handling, plus submenu buttons for touch screens.
- `js/code-copy.js`: a Copy button for code blocks in post content.

### Build and docs

- `dist/` holds the compiled files. A GitHub Action compiles every pull request and commits `dist/` on `develop`.
- `docs/build-a-theme.md`: a complete ClassicPress theme, step by step.

### Known limits

- Breakpoints are fixed, because media queries can't read custom properties.
- `Providers\Backdrop` and the ClassicPress add-on haven't been tested on a live ClassicPress site yet.
- `docs/build-a-theme.md` hasn't been followed from start to finish yet.
