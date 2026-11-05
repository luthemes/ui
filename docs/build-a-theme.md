# Build a theme from scratch

This guide builds **Starter**, a small ClassicPress theme, using every part of Luthemes UI. By the end you'll know which parts come from the package, which parts belong to your theme, and how they connect.

It uses plain ClassicPress functions (no framework), so you can see exactly what the package does. [Step 10](#10-optional-use-backdrop-and-the-php-helpers) adds Backdrop at the end.

## What you'll build

```
starter/
├── composer.json          gets luthemes/ui
├── package.json           Vite + Sass
├── vite.config.js         compiles resources/ into public/
├── style.css              theme header (ClassicPress needs it)
├── functions.php          loads the compiled CSS and JS, registers menu + sidebar
├── header.php             .app, .app-header, .app-nav
├── index.php              .app-content, the post loop
├── sidebar.php            .app-sidebar
├── footer.php             .app-footer
└── resources/
    ├── js/
    │   └── app.js         turns on the mobile menu
    └── scss/
        ├── screen.scss    loads the package, then your styles
        └── 07.components/
            ├── _index.scss
            └── 01.entry/
                └── _index.scss
```

The build writes `public/css/screen.css` and `public/js/app.js`. Those are what the site loads.

## 1. Create the theme folder and `style.css`

In `wp-content/themes/`, make a folder called `starter` and add `style.css`. ClassicPress reads the theme's name from this comment. The real styles come from `public/css/screen.css`, so this file stays empty otherwise.

```css
/*
 * Theme Name:  Starter
 * Description: Learning theme built on Luthemes UI.
 * Version:     0.1.0
 * Author:      Your Name
 * Requires PHP: 7.4
 * License:     GNU General Public License v2 or later
 * Text Domain: starter
 */
```

## 2. Get the package with Composer

`composer.json`:

```json
{
	"name": "you/starter",
	"type": "wordpress-theme",
	"repositories": [
		{ "type": "vcs", "url": "https://github.com/luthemes/ui" }
	],
	"require": {
		"php": ">=7.4",
		"luthemes/ui": "dev-main"
	}
}
```

```bash
composer install
```

You now have `vendor/luthemes/ui/` with `scss/`, `js/`, and `src/` inside. Look around: every SCSS file starts with a comment explaining what it does.

> The repository entry and `dev-main` are only needed until the package is on Packagist and tagged. Then it becomes `"luthemes/ui": "^0.1"`.

## 3. Set up Vite

`package.json`:

```json
{
	"private": true,
	"type": "module",
	"scripts": {
		"build": "vite build",
		"watch": "vite build --watch"
	},
	"devDependencies": {
		"sass": "^1.95.0",
		"vite": "^7.2.7"
	}
}
```

`vite.config.js`:

```js
import { defineConfig } from 'vite';

export default defineConfig( {
	// We write into public/, so turn off Vite's own public folder.
	publicDir: false,

	css: {
		preprocessorOptions: {
			// Lets SCSS find 'luthemes/ui/scss' inside vendor/.
			scss: { loadPaths: [ 'vendor' ] },
		},
	},

	build: {
		outDir: 'public',
		emptyOutDir: true,
		rollupOptions: {
			input: {
				app:    'resources/js/app.js',
				screen: 'resources/scss/screen.scss',
			},
			output: {
				// Fixed names (no hashes) keep the PHP simple.
				entryFileNames: 'js/[name].js',
				assetFileNames: 'css/[name][extname]',
			},
		},
	},
} );
```

```bash
npm install
```

The one line that connects Vite to the package is `loadPaths: [ 'vendor' ]`.

## 4. Write the stylesheet

`resources/scss/screen.scss` is short on purpose. It loads the package with your theme's settings, then your own partials.

```scss
////
/// Starter: main stylesheet.
////

// 1. The package, with this theme's settings. Only list what you change.
@use 'luthemes/ui/scss' with (
	$colors: (
		header:     #1d3557,
		footer:     #1d3557,
		primary:    #e63946,
		link:       #1d3557,
		link-hover: #e63946,
		heading:    #1d3557,
	),
	$font-family-heading: ( Georgia, serif )
);

// 2. This theme's own styles.
@forward '07.components';
```

What you just got from the package, in order:

1. **settings**: your colors merged over the defaults.
2. **tools**: nothing printed yet.
3. **generic**: `:root { --color-header: #1d3557; … }` and the reset.
4. **elements**: body text, headings, links, tables.
5. **objects**: `.container`, `.section`, `.grid`.
6. **layouts**: `.app`, `.app-header`, `.app-nav`, `.app-content`, `.app-footer`.
7. **components**: `.card`, `.badge`, `.button`, `.section-header`.
8. **utilities**: `.screen-reader-text`, `.hide-mobile`…

Now add something that's only yours. Number your folders the same way the package does (`07.components` is the components layer). `resources/scss/07.components/_index.scss`:

```scss
@forward '01.entry';
```

`resources/scss/07.components/01.entry/_index.scss`:

```scss
////
/// Entry: one post in the loop. This is the theme's own look.
////

// Load only the tools: mixins and functions, no CSS output.
@use 'luthemes/ui/scss/02.tools' as ui;

.entry {
	&__title {
		font-size: 1.5rem;

		a {
			text-decoration: none;
		}

		// Bigger titles from tablets up.
		@include ui.up( medium ) {
			font-size: 2rem;
		}
	}

	&__meta {
		color: ui.color( muted );    // var( --color-muted )
		font-size: 0.875rem;
		margin-bottom: ui.space( m ); // var( --space-m )
	}
}
```

Notice the split: the **package** gives you `.card`, the grid, and the breakpoints. The **theme** decides what a post title looks like.

## 5. Turn on the mobile menu

`resources/js/app.js`:

```js
import { appNav } from '../../vendor/luthemes/ui/js/app-nav.js';

appNav();
```

Vite bundles the package's menu script into `public/js/app.js`.

## 6. Build

```bash
npm run build
```

You should see `public/css/screen.css` and `public/js/app.js` (Vite may also write a near-empty `js/screen.js` for the stylesheet entry; ignore it). Open the CSS and find your `--color-header: #1d3557` near the top.

If the build says it can't find `luthemes/ui/scss`, run `composer install` first. If it says `Unknown breakpoint` or `Unknown color`, there's a typo in a name, and the error lists the valid ones.

## 7. Load the files and register the menu: `functions.php`

```php
<?php
/**
 * Starter theme setup.
 */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );

	register_nav_menus( [
		'primary' => __( 'Primary', 'starter' ),
	] );
} );

add_action( 'widgets_init', function () {
	register_sidebar( [
		'id'            => 'primary',
		'name'          => __( 'Primary Sidebar', 'starter' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget__title">',
		'after_title'   => '</h2>',
	] );
} );

add_action( 'wp_enqueue_scripts', function () {
	$css = 'public/css/screen.css';
	$js  = 'public/js/app.js';

	// filemtime() changes the version on every build, so browsers re-download.
	wp_enqueue_style( 'starter', get_theme_file_uri( $css ), [], filemtime( get_theme_file_path( $css ) ) );
	wp_enqueue_script( 'starter', get_theme_file_uri( $js ), [], filemtime( get_theme_file_path( $js ) ), true );
} );

// Vite outputs an ES module, so load it as one.
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	return 'starter' === $handle ? str_replace( '<script ', '<script type="module" ', $tag ) : $tag;
}, 10, 2 );
```

Each widget gets the package's `widget` class, so the sidebar is styled with no extra CSS.

## 8. The templates

### `header.php`: `.app`, `.app-header`, `.app-nav`

```php
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'starter' ); ?></a>

<div class="app">

	<header class="app-header">
		<div class="app-header__inner container">

			<div class="app-header__branding">
				<a class="app-header__title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<span class="app-header__tagline"><?php bloginfo( 'description' ); ?></span>
			</div>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav class="app-nav" aria-label="<?php esc_attr_e( 'Primary', 'starter' ); ?>">
					<button class="app-nav__toggle" type="button" aria-expanded="false" aria-controls="app-nav-menu">
						<?php esc_html_e( 'Menu', 'starter' ); ?>
					</button>
					<?php wp_nav_menu( [
						'theme_location' => 'primary',
						'container'      => false,
						'menu_id'        => 'app-nav-menu',
						'menu_class'     => 'app-nav__menu',
						'depth'          => 3,
					] ); ?>
				</nav>
			<?php endif; ?>

		</div>
	</header>
```

`wp_nav_menu()` prints `<ul id="app-nav-menu" class="app-nav__menu">` with plain `<li>` and nested `<ul class="sub-menu">`. The package styles the menu by that structure, so nothing else is needed. The current page gets `.current-menu-item`, which the package underlines.

### `index.php`: `.app-content`, cards in a grid

```php
<?php get_header(); ?>

	<div class="app-content app-content--sidebar-right container">

		<main id="main" class="app-main">

			<?php if ( have_posts() ) : ?>

				<div class="grid grid--2">
					<?php while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'card entry' ); ?>>
							<h2 class="entry__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="entry__meta"><?php echo esc_html( get_the_date() ); ?></div>
							<?php the_excerpt(); ?>
							<a class="button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'starter' ); ?></a>
						</article>

					<?php endwhile; ?>
				</div>

				<?php the_posts_pagination(); ?>

			<?php else : ?>
				<p><?php esc_html_e( 'Nothing here yet.', 'starter' ); ?></p>
			<?php endif; ?>

		</main>

		<?php get_sidebar(); ?>

	</div>

<?php get_footer(); ?>
```

Each class on an article comes from a different place:
- `card` comes from the package: the dotted box, with the button pinned to the bottom.
- `entry` comes from your theme: the title and date styles in `01.entry`.
- `grid grid--2` comes from the package: one column on phones, two from 768px.

### `sidebar.php`: `.app-sidebar`

```php
<?php if ( is_active_sidebar( 'primary' ) ) : ?>
	<aside class="app-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'starter' ); ?>">
		<?php dynamic_sidebar( 'primary' ); ?>
	</aside>
<?php endif; ?>
```

### `footer.php`: `.app-footer`

```php
	<footer class="app-footer">
		<div class="app-footer__inner container">
			<p class="app-footer__credit">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
		</div>
	</footer>

</div><!-- .app -->

<?php wp_footer(); ?>
</body>
</html>
```

## 9. Activate and try it

1. Activate **Starter** in *Appearance → Themes*.
2. Create a menu in *Appearance → Menus*, add a few pages and a submenu, and assign it to **Primary**.
3. Add a couple of widgets to **Primary Sidebar**.

Then make the browser narrow:

| Width | What changes | Where it comes from |
|---|---|---|
| under 640px | Tagline hides | `06.layouts/02.header`, `up( small )` |
| under 768px | Menu collapses behind the **Menu** button; posts go to one column | `06.layouts/03.navigation` and `05.objects/03.grids`, `medium` |
| under 1024px | Sidebar moves below the posts | `06.layouts/04.content`, `up( large )` |

### Experiments

Each one is a one-line change. Rebuild with `npm run build` after each, or keep `npm run watch` running.

- **Move every breakpoint at once.** Add `$breakpoints: ( medium: 900px )` to the `with ( … )` in `screen.scss`. The menu, grid, and `ui.up( medium )` in your `01.entry` all switch at 900px together. This is the point of sharing breakpoints.
- **Sidebar on the left.** In `index.php`, change `app-content--sidebar-right` to `app-content--sidebar-left`.
- **No sidebar.** Remove the `--sidebar-right` class and `get_sidebar()`.
- **Recolor without rebuilding.** Add `:root { --color-header: #2a9d8f; }` to the Customizer's *Additional CSS*. The header changes immediately. Colors are custom properties, so they can change at run time.
- **Break it on purpose.** Write `@include ui.up( tablet )` in `01.entry`. The build fails with `Unknown breakpoint 'tablet'` and lists the real names.

## 10. Optional: use Backdrop and the PHP helpers

If the theme is built on [Backdrop](https://github.com/backdrop-dev/framework) like the other Luthemes themes, register the package's provider, and the helpers are available anywhere:

```bash
composer require backdrop-dev/framework
```

```php
// app/framework.php (or functions.php)
$theme = new Backdrop\Core\Application();
$theme->provider( Luthemes\UI\Providers\Backdrop::class );
$theme->boot();
```

```php
<?php $ui = Backdrop\App::resolve( 'luthemes.ui' ); ?>

<?= $ui->badge( 'Featured' ); ?>
<?= $ui->button( 'Read more', get_permalink(), [ 'primary' ] ); ?>
```

They print the same markup you'd write by hand (`<span class="badge">…`), escaped. When you use them, ship the `vendor/` folder with the theme. The styles-only theme above doesn't need it: only `public/` is loaded at run time.

## What to ship

For a theme that only uses the styles and menu script, the ClassicPress site needs:

```
style.css  functions.php  header.php  index.php  sidebar.php  footer.php  public/
```

Leave out `node_modules/`, `resources/`, and the build files if you like (keeping `resources/` is friendly to people who want to rebuild). Add `vendor/` only if the theme calls the PHP helpers.

## The same package on a Novaris site

The SCSS and JS steps (2–6) are identical. Only the PHP side differs: register `Luthemes\UI\Providers\Novaris::class` in `config/app.php`, and use the same class names in your views (`resources/views/header/default.php` and so on). luthemes.com is a working example.
