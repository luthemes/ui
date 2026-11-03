# Luthemes UI

Shared styles and UI building blocks for Luthemes projects. One package, used the same way by **ClassicPress themes** (built on [Backdrop](https://github.com/backdrop-dev/framework)) and **[Novaris](https://github.com/novaris-dev/framework) sites**:

- **SCSS:** design tokens, layout objects, and components, compiled by your project's own build (Vite + Sass).
- **PHP:** small helpers that print component markup, with a service provider for each framework.

## Requirements

- PHP 7.4 or later
- Composer
- Node and a Sass build (e.g. Vite + `sass`) to compile the SCSS

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

## Styles

Composer installs the SCSS into `vendor/luthemes/ui/scss`. Tell Sass to look in `vendor`. With Vite:

```ts
// vite.config.ts
export default defineConfig( {
	css: {
		preprocessorOptions: {
			scss: { loadPaths: [ 'vendor' ] },
		},
	},
} );
```

Then forward it from your stylesheet, either all at once:

```scss
@forward 'luthemes/ui/scss';
```

or, if your project follows ITCSS, each layer in its place:

```scss
@forward 'luthemes/ui/scss/tokens';      // in your generics
@forward 'luthemes/ui/scss/objects';     // in your objects
@forward 'luthemes/ui/scss/components';  // in your components
```

Run `composer install` **before** your build (`npm run build`) so Sass can find the files.

### Tokens

CSS custom properties on `:root`. Override any of them in your own stylesheet to restyle everything that uses them.

| Token | Default |
|---|---|
| `--color-black`, `--color-white` | `#000000`, `#ffffff` |
| `--color-text`, `--color-muted` | `#677d89`, `#555555` |
| `--color-surface`, `--color-surface-alt` | `#ffffff`, `#f5f5f5` |
| `--color-rule` | `#cccccc` |
| `--space-xs` … `--space-xl` | `0.25rem`, `0.5rem`, `0.75rem`, `1.125rem`, `2.5rem` |
| `--border-width`, `--border-dotted`, `--border-solid` | `0.063rem` dotted / solid black |
| `--width-wide`, `--width-medium`, `--width-narrow` | `80rem`, `64rem`, `50rem` |

### Objects (layout)

| Class | What it does |
|---|---|
| `.container` · `--medium` · `--narrow` | Centered, max-width, side padding |
| `.section` · `--muted` | Full-width band with vertical padding; `--muted` is light gray |
| `.grid` · `--2` · `--3` | Equal columns; one column under 768px. Works on lists |

### Components

| Class | What it does |
|---|---|
| `.card` · `--muted` | Dotted box; its last child (a link or button) sits at the bottom |
| `.badge` · `--large` | Small black uppercase label |
| `.button` · `--primary`, `.buttons` | Outlined / filled button; a wrapping row of buttons |
| `.section-header` (`__title`, `__description`) | Centered heading block |

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

## PHP

### Novaris

Register the provider in `config/app.php`:

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

### Helpers

| Method | Returns |
|---|---|
| `badge( $text, $modifiers = [] )` | `<span class="badge …">` |
| `button( $text, $url, $modifiers = [] )` | `<a class="button …">` |
| `classes( $block, $modifiers = [] )` | `'button button--primary'` |
| `scssPath( $append = '' )` | Path to the package's SCSS |

Text and URLs are escaped for you.

## License

GPL-2.0-or-later. See [license.md](license.md).
