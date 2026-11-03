# Changelog

## 0.1.0 - Unreleased

### Added
- SCSS in ITCSS layers, loaded together with `@use 'luthemes/ui/scss'`:
  - **settings**: breakpoints, colors, spacing, typography, widths. Each is overridable with `@use … with ( … )`; maps merge over the defaults.
  - **tools**: `up()` / `down()` breakpoint mixins and `breakpoint()`, `color()`, `space()`, `width()` functions, with build errors for unknown names.
  - **generic**: `:root` custom properties built from the settings, and a small reset.
  - **elements**: body text, headings, links, focus outline, tables.
  - **objects**: `.container`, `.section`, `.grid` (`--2`, `--3`, `--4`), mobile first.
  - **layouts**: the page skeleton: `.app`, `.app-header`, `.app-nav` (mobile menu and dropdowns), `.app-content` with `--sidebar-left` / `--sidebar-right`, `.app-main`, `.app-sidebar`, `.app-footer`.
  - **components**: `.card`, `.badge`, `.button` (and `.buttons`), `.section-header`.
  - **utilities**: `.screen-reader-text`, `.skip-link`, `.hide-mobile`, `.hide-desktop`, text helpers.
- `js/app-nav.js`: mobile menu toggle with `aria-expanded`, Escape, click-outside, and resize handling.
- `Luthemes\UI\UI` with `badge()`, `button()`, `classes()`, `path()`, `scssPath()`, and `jsPath()` helpers.
- Service providers for Backdrop (ClassicPress themes) and Novaris sites.
- `docs/build-a-theme.md`: a complete ClassicPress theme built on the package, step by step.
