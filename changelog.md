# Changelog

## 0.1.0 - Unreleased

### Added
- SCSS in numbered ITCSS layers (`01.settings` … `08.utilities`, one `NN.name/_index.scss` folder per piece, as in novaris-dev/novaris), loaded together with `@use 'luthemes/ui/scss'`:
  - **settings**: breakpoints, colors, spacing, typography, widths. Each is overridable with `@use … with ( … )`; maps merge over the defaults.
  - **tools**: `up()` / `down()` breakpoint mixins and `breakpoint()`, `color()`, `space()`, `width()` functions, with build errors for unknown names.
  - **generic**: `:root` custom properties built from the settings, and a small reset.
  - **elements**: body text, headings, links, focus outline, tables.
  - **objects**: `.container`, `.section`, `.grid` (`--2`, `--3`, `--4`), mobile first.
  - **layouts**: the page skeleton: `.app`, `.app-header`, `.app-nav` (mobile menu and dropdowns), `.app-content` with `--sidebar-left` / `--sidebar-right`, `.app-main`, `.app-sidebar`, `.app-footer`.
  - **components**: `.card`, `.badge`, `.button` (and `.buttons`), `.section-header`.
  - **utilities**: `.screen-reader-text` (shown on focus), `.skip-link`, editor alignments (`.alignleft`, `.alignright`, `.aligncenter`, `.alignwide`, `.alignfull`), `.hide-mobile`, `.hide-desktop`, text helpers.
- `js/app-nav.js`: mobile menu toggle with `aria-expanded`, Escape, click-outside, and resize handling.
- `Luthemes\UI\UI` with `badge()`, `button()`, `classes()`, `path()`, `scssPath()`, and `jsPath()` helpers.
- Service providers for Backdrop (ClassicPress themes) and Novaris sites.
- `docs/build-a-theme.md`: a complete ClassicPress theme built on the package, step by step.
- Design settings, each with a matching custom property: `$radius`, `$shadow`, `$box-border-style`, `$box-border-bottom-style`, `$font-weight-heading`, `$heading-sizes`, `$header-padding`, `$header-title-size`, `$header-title-size-large`, `$header-tagline-size`, `$nav-link-padding`, `$button-border-width`, `$button-padding`, `$badge-font-size`, `$badge-letter-spacing`, `$badge-padding`, `$badge-text-transform`. Their defaults keep the existing look.
- `border` color, used by box borders. It defaults to black, so dark themes should set it.
- `.app-header--full`, which lets the header row run the full width of the screen.
- The menu script and styles accept the `.menu-toggle` button that Novaris prints.
- Components for all platforms: `.pagination`, `.widget` and `.widget-title`, and `.social-menu`, with a `$pagination-padding` setting.
- `platforms/classicpress`: an optional add-on for ClassicPress, with captions, sticky posts, threaded comments, the comment form, the search form, the calendar widget, and `the_posts_pagination()`.

### Fixed
- `--border-dotted` and `--border-solid` no longer use `--color-black`, which was almost invisible on dark themes. They use `--color-border`.
- The docs used `@use '07.components'`, which stops the build. They now use `@forward`.
