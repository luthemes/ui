# Luthemes UI

Shared styles and UI building blocks for Luthemes projects. One package, used the same way by **ClassicPress themes** (built on [Backdrop](https://github.com/backdrop-dev/framework)) and **[Novaris](https://github.com/novaris-dev/framework) sites**:

- **SCSS:** settings, a reset, base element styles, the page skeleton (header, mobile menu, content with sidebar, footer), layout objects, components, and utilities. Your project's own build (Vite + Sass) compiles it.
- **JS:** the mobile menu toggle.
- **PHP:** small helpers that print component markup, with a service provider for each framework.

**New here?** [Build a theme from scratch](docs/build-a-theme.md) walks through a complete ClassicPress theme that uses every part of the package.

## How it's organized

The SCSS follows ITCSS: layers go from the most general to the most specific, and each layer can use everything above it.

The numbers are the load order, the same convention as [novaris-dev/novaris](https://github.com/novaris-dev/novaris/tree/develop/resources/scss). Each piece is a numbered folder with an `_index.scss` (`scss/01.settings/02.colors/_index.scss`).

| Layer | Folder | What's in it | Outputs CSS? |
|---|---|---|---|
| Settings | `scss/01.settings` | `01.breaks`, `02.colors`, `03.fonts`, `04.sizings`, `05.spacings`, `06.design` | No |
| Tools | `scss/02.tools` | `01.breakpoints`: `up()` / `down()` mixins; `02.functions`: `color()` / `space()` / `width()` | No |
| Generics | `scss/03.generics` | `01.tokens`: `:root` custom properties; `02.resets` | Yes |
| Elements | `scss/04.elements` | `01.defaults`, `02.headings`, `03.links`, `04.tables` | Yes |
| Objects | `scss/05.objects` | `01.containers`, `02.sections`, `03.grids` | Yes |
| Layouts | `scss/06.layouts` | `01.app`, `02.header`, `03.navigation`, `04.content`, `05.footer` | Yes |
| Components | `scss/07.components` | `01.cards`, `02.badges`, `03.buttons`, `04.section-headers` | Yes |
| Utilities | `scss/08.utilities` | `01.a11y`, `02.alignments`, `03.display`, `04.text` | Yes |

**What belongs in the package, and what stays in your theme:** the package handles structure (how the header lays out, when the menu collapses, how a grid drops to one column). Your theme handles the look (colors, fonts, the style of posts and comments, anything unique). Colors and spacing are settings, so each theme sets its own.

## Requirements

- PHP 7.4 or later and Composer
- Node with Vite and `sass` (or any Sass build) to compile the SCSS

## Install

```bash
composer require luthemes/ui
```

Until the package is on Packagist, add the repository to your `composer.json` first:

```json
"repositories": [
	{ "type": "vcs", "url": "https://github.com/luthemes/ui" }
]
```

Run `composer install` **before** `npm run build`, so Sass can find the files.

## Styles

Composer puts the SCSS in `vendor/luthemes/ui/scss`. Tell Sass to look in `vendor`. With Vite:

```js
// vite.config.js
export default defineConfig( {
	css: {
		preprocessorOptions: {
			scss: { loadPaths: [ 'vendor' ] },
		},
	},
} );
```

Load it **first** in your main stylesheet, then your own styles:

```scss
// resources/scss/screen.scss
@use 'luthemes/ui/scss';
@forward '07.components';   // your theme's own partials
```

### Change the settings

Pass overrides with `with ( … )`. You only list what you change; everything else keeps its default.

```scss
@use 'luthemes/ui/scss' with (
	$colors: (
		header:  #1d3557,
		footer:  #1d3557,
		primary: #e63946,
	),
	$breakpoints: ( medium: 782px ),
	$font-family-body: ( 'Merriweather', serif )
);
```

Colors, spacing, and widths also become CSS custom properties, so you can change them later in plain CSS (for example from a Customizer setting):

```css
:root { --color-header: #264653; }
```

### Use the tools in your own partials

Load just the tools: they output no CSS, so this is safe in any partial.

```scss
// resources/scss/07.components/01.entry/_index.scss
@use 'luthemes/ui/scss/02.tools' as ui;

.entry {
	padding: ui.space( m );                 // var( --space-m )
	border-bottom: var( --border-rule );

	@include ui.up( medium ) {              // 768px and wider
		padding: ui.space( xl );
	}
}
```

`ui.color( name )`, `ui.space( name )`, and `ui.width( name )` return the matching `var( --… )`, and fail the build if the name doesn't exist.

## Settings reference

| Setting | Defaults | Custom properties |
|---|---|---|
| `$breakpoints` | `small` 40rem (640px), `medium` 48rem (768px), `large` 64rem (1024px), `wide` 80rem (1280px) | none (media queries can't use custom properties) |
| `$colors` | `black`, `white`, `text`, `heading`, `muted`, `link`, `link-hover`, `background`, `surface`, `surface-alt`, `rule`, `border`, `primary`, `on-primary`, `header`, `on-header`, `footer`, `on-footer` | `--color-*` |
| `$spacing` | `xs` .25rem, `s` .5rem, `m` .75rem, `l` 1.125rem, `xl` 2.5rem | `--space-*` |
| `$widths` | `wide` 80rem, `medium` 64rem, `narrow` 50rem, `sidebar` 18rem | `--width-*` |
| `$font-family-body`, `$font-family-heading`, `$font-family-code` | system fonts | `--font-family-*` |
| `$font-size-base`, `$line-height-base`, `$line-height-heading` | 1.0625rem, 1.7, 1.25 | same names |
| `$border-width` | 0.063rem | `--border-width`, `--border-dotted`, `--border-solid`, `--border-rule` |

`on-*` colors are the text color that sits on that background (`on-header` is the header's text).

### Design settings

These control the look of the shared pieces, so each theme can have its own design while the package stays the same. The defaults keep the package's standard look.

| Setting | Default | Custom property |
|---|---|---|
| `$radius` | `0` | `--radius` (cards, buttons, badges) |
| `$shadow` | `none` | `--shadow` (cards) |
| `$box-border-style`, `$box-border-bottom-style` | `dotted`, `solid` | `--border-box`, `--border-box-bottom` |
| `$font-weight-heading` | `700` | `--font-weight-heading` |
| `$heading-sizes` | `h1` 2.25rem, `h2` 1.75rem, `h3` 1.375rem, `h4` 1.125rem, `h5` 1rem, `h6` 0.875rem | `--font-size-h1` to `--font-size-h6` |
| `$header-padding` | `var( --space-m )` | `--header-padding` |
| `$header-title-size`, `$header-title-size-large` | 1.5rem, 1.75rem | `--header-title-size`, `--header-title-size-large` |
| `$header-tagline-size` | 0.9375rem | `--header-tagline-size` |
| `$nav-link-padding` | `var( --space-s ) var( --space-m )` | `--nav-link-padding` |
| `$button-border-width`, `$button-padding` | 0.125rem, `var( --space-s ) 1.25rem` | `--button-border-width`, `--button-padding` |
| `$badge-font-size`, `$badge-letter-spacing`, `$badge-padding`, `$badge-text-transform` | 0.75rem, 0.08em, 0.2rem 0.6rem, `uppercase` | `--badge-font-size`, `--badge-letter-spacing`, `--badge-padding`, `--badge-text-transform` |
| `$pagination-padding` | `var( --space-xs ) var( --space-m )` | `--pagination-padding` |

```scss
@use 'luthemes/ui/scss' with (
	$colors: ( background: #0f1117, text: #d7dae0, border: #2d3446 ),
	$radius: 0.5rem,
	$box-border-style: solid,
	$header-padding: var( --space-s )
);
```

Dark themes should set `border`, because it defaults to black.

## Layouts: the page skeleton

```html
<body>
<a class="skip-link screen-reader-text" href="#main">Skip to content</a>
<div class="app">

	<header class="app-header">
		<div class="app-header__inner container">
			<div class="app-header__branding">
				<a class="app-header__title" href="/">Site Title</a>
				<span class="app-header__tagline">Tagline</span>
			</div>
			<nav class="app-nav" aria-label="Primary">
				<button class="app-nav__toggle" type="button" aria-expanded="false" aria-controls="app-nav-menu">Menu</button>
				<ul class="app-nav__menu" id="app-nav-menu">
					<li><a href="/" aria-current="page">Home</a></li>
					<li><a href="/blog">Blog</a>
						<ul><li><a href="/blog/news">News</a></li></ul>
					</li>
				</ul>
			</nav>
		</div>
	</header>

	<div class="app-content app-content--sidebar-right container">
		<main id="main" class="app-main">…</main>
		<aside class="app-sidebar">…</aside>
	</div>

	<footer class="app-footer">
		<div class="app-footer__inner container">
			<p class="app-footer__credit">© 2026 Site Title</p>
		</div>
	</footer>

</div>
</body>
```

| Class | What it does |
|---|---|
| `.app` | Wraps the page; keeps the footer at the bottom on short pages |
| `.app-header` (`__inner`, `__branding`, `__title`, `__tagline`) · `--full` | Branding left, menu right. Tagline hides on phones. `--full` lets the row run the full width of the screen |
| `.app-nav` (`__toggle`, `__menu`) | Phones: Menu button opens a panel below the header. `medium` and up: a row of links with dropdowns |
| `.app-content` · `--sidebar-right` · `--sidebar-left` | Content area; the sidebar sits beside the main column from `large` up and stacks below it before that |
| `.app-main`, `.app-sidebar` | The two columns |
| `.app-footer` (`__inner`, `__credit`) | Footer band |

The menu is styled by its structure (`li`, `a`, nested `ul`), so the markup from ClassicPress's `wp_nav_menu()` and Novaris's menu works once the container has `.app-nav` and the list has `.app-nav__menu`. In Novaris, pass those as `container_class` and `menu_class`. The button can be `.app-nav__toggle` or the `.menu-toggle` that Novaris prints. The current page is underlined, marked by `aria-current="page"`, `.current-menu-item`, or `.menu-items__item--current`.

## Objects

| Class | What it does |
|---|---|
| `.container` · `--medium` · `--narrow` | Centered, max-width, side padding |
| `.section` · `--muted` | Full-width band with vertical padding; `--muted` is light gray |
| `.grid` · `--2` · `--3` · `--4` | One column on phones; 2 or 3 columns from `medium`; `--4` is 2 then 4 from `large`. Works on lists |

## Components

| Class | What it does |
|---|---|
| `.card` · `--muted` | Dotted box; its last child (a link or button) sits at the bottom |
| `.badge` · `--large` | Small uppercase label in the `primary` color |
| `.button` · `--primary`, `.buttons` | Outlined / filled button; a wrapping row of buttons |
| `.section-header` (`__title`, `__description`) | Centered heading block |
| `.pagination` (`__items`, `__item`, `__anchor`) | Numbered page links. Novaris's default `pagination-items` classes work too |
| `.widget`, `.widget-title` | Sidebar boxes, as printed by Novaris and ClassicPress |
| `.social-menu` | A row of icon links |

```html
<section class="section section--muted">
	<div class="container">
		<header class="section-header">
			<h2 class="section-header__title">Latest Posts</h2>
		</header>
		<ul class="grid grid--3">
			<li class="card">
				<span class="badge">News</span>
				<h3>Title</h3>
				<p>Summary…</p>
				<a class="button" href="#">Read more</a>
			</li>
		</ul>
	</div>
</section>
```

## Platforms

The core works on Novaris and ClassicPress. ClassicPress prints some markup that themes can't rename, so that lives in an add-on. Load it after the package:

```scss
@use 'luthemes/ui/scss' with ( … );
@use 'luthemes/ui/scss/platforms/classicpress';
```

It covers captions, sticky posts, threaded comments and the comment form, the search form, the calendar widget, and `the_posts_pagination()`. It uses the same settings as the core, so a theme's colors and shapes apply here too. Novaris sites don't need it.

## Utilities

| Class | What it does |
|---|---|
| `.screen-reader-text` | Hidden visually, still read by screen readers |
| `.skip-link` | With `.screen-reader-text`: appears on keyboard focus (any `.screen-reader-text` does) |
| `.alignleft`, `.alignright`, `.aligncenter`, `.alignwide`, `.alignfull` | The editor's alignment classes; `.alignwide` uses `--width-wide` |
| `.hide-mobile`, `.hide-desktop` | Hide below / from the `medium` breakpoint |
| `.text-left`, `.text-center`, `.text-right`, `.text-muted`, `.text-small` | Text helpers |

## JavaScript

The mobile menu needs `js/app-nav.js`. Import it into your own script and your build bundles it:

```js
// resources/js/app.js
import { appNav } from '../../vendor/luthemes/ui/js/app-nav.js';

appNav();
```

It toggles `.app-nav--open` and `aria-expanded`, closes on Escape (returning focus to the button), on a click outside, and when the screen widens past the mobile breakpoint.

## PHP

### Novaris

The helpers are for sites, not distributed themes. An exported theme doesn't include `vendor`, and a site's `providers` setting replaces the theme's, so a theme should write the class names directly. To use the helpers on a site, require the package in that site's `composer.json`, then register the provider in `config/app.php`:

```php
'providers' => [
	Luthemes\UI\Providers\Novaris::class,
],
```

```php
<?= app( 'luthemes.ui' )->button( 'Download', $url, [ 'primary' ] ); ?>
```

### ClassicPress (Backdrop)

Register the provider in your theme's `app/framework.php`:

```php
$theme->provider( Luthemes\UI\Providers\Backdrop::class );
```

```php
<?= Backdrop\App::resolve( 'luthemes.ui' )->badge( 'Featured' ); ?>
```

Each project only loads its own provider, so a ClassicPress theme never needs Novaris and a Novaris site never needs Backdrop.

### ClassicPress without a framework

`Luthemes\UI\UI` has no framework dependency, so a theme can load Composer's autoloader and create it directly:

```php
$ui = new Luthemes\UI\UI();
echo $ui->button( 'Download', $url, [ 'primary' ] );
```

### Helpers

| Method | Returns |
|---|---|
| `badge( $text, $modifiers = [] )` | `<span class="badge …">` |
| `button( $text, $url, $modifiers = [] )` | `<a class="button …">` |
| `classes( $block, $modifiers = [] )` | `'button button--primary'` |
| `scssPath( $append = '' )`, `jsPath( $append = '' )` | Paths to the package's SCSS and JS |

Text and URLs are escaped for you.

## License

GPL-2.0-or-later. See [license.md](license.md).
