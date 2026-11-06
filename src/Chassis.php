<?php
/**
 * Chassis.
 *
 * Framework-agnostic helpers for the shared UI: where the package lives (so
 * build tools can find the SCSS) and small functions that print the markup
 * for its components. Works the same in ClassicPress themes (Backdrop) and
 * Novaris sites; each framework gets a thin service provider in `Providers/`.
 *
 * @package   Techkodeninja\Chassis
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2026 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/techkodeninja/chassis
 */

namespace Techkodeninja\Chassis;

class Chassis
{
	/**
	 * Package version.
	 */
	const VERSION = '0.0.5';

	/**
	 * The platform this was created for: `novaris`, `backdrop`, or `plain`.
	 *
	 * @var string
	 */
	protected $platform;

	/**
	 * Turns a file in `dist/` into a URL. Set by the service providers.
	 *
	 * @var callable|null
	 */
	protected $asset;

	/**
	 * `chassis.json` files to read, parent theme first. Later files override
	 * earlier ones.
	 *
	 * @var string[]
	 */
	protected $designFiles = [];

	/**
	 * Spacing names that `chassis.json` can use instead of CSS values.
	 */
	const SPACE_NAMES = [ 'xs', 's', 'm', 'l', 'l-plus', 'xl' ];

	/**
	 * Where the Menu button sits on phones, for `menu.toggle` in
	 * `chassis.json`. `bottom-left` is the default, so it sets nothing.
	 */
	const MENU_TOGGLE = [
		'bottom-left'  => [],
		'bottom-right' => [
			'menu-toggle-left'  => 'auto',
			'menu-toggle-right' => 'var( --space-l )',
		],
		'top-right'    => [
			'menu-toggle-background' => 'transparent',
			'menu-toggle-border'     => 'var( --border-width ) solid currentColor',
			'menu-toggle-bottom'     => 'auto',
			'menu-toggle-left'       => 'auto',
			'menu-toggle-padding'    => 'var( --space-xs ) var( --space-m )',
			'menu-toggle-position'   => 'static',
			'menu-panel-bottom'      => 'auto',
			'menu-panel-inset'       => '0',
			'menu-panel-position'    => 'absolute',
			'menu-panel-top'         => '100%',
		],
		'top-left'     => [
			'menu-toggle-background' => 'transparent',
			'menu-toggle-border'     => 'var( --border-width ) solid currentColor',
			'menu-toggle-bottom'     => 'auto',
			'menu-toggle-left'       => 'auto',
			'menu-toggle-padding'    => 'var( --space-xs ) var( --space-m )',
			'menu-toggle-position'   => 'static',
			'menu-panel-bottom'      => 'auto',
			'menu-panel-inset'       => '0',
			'menu-panel-position'    => 'absolute',
			'menu-panel-top'         => '100%',
			'menu-toggle-order'      => '-1',
			'menu-header-justify'    => 'flex-start',
		],
	];

	/**
	 * Sets the platform and how to reach the files in `dist/`. The service
	 * providers pass these in, and `plain` means no provider is in use.
	 */
	public function __construct( string $platform = 'plain', ?callable $asset = null )
	{
		$this->platform = $platform;
		$this->asset    = $asset;
	}

	/**
	 * Returns the platform: `novaris`, `backdrop`, or `plain`.
	 */
	public function platform(): string
	{
		return $this->platform;
	}

	/**
	 * Returns the package's root path, with an optional appended path.
	 */
	public function path( string $append = '' ): string
	{
		$path = dirname( __DIR__ );

		return $append ? $path . '/' . ltrim( $append, '/' ) : $path;
	}

	/**
	 * Returns the path to the SCSS source, e.g. for a Sass load path.
	 */
	public function scssPath( string $append = '' ): string
	{
		return $this->path( 'scss' . ( $append ? '/' . ltrim( $append, '/' ) : '' ) );
	}

	/**
	 * Returns the path to a built file in `dist/`.
	 */
	public function distPath( string $append = '' ): string
	{
		return $this->path( 'dist' . ( $append ? '/' . ltrim( $append, '/' ) : '' ) );
	}

	/**
	 * Returns the URL of a built file, or an empty string if it can't be
	 * reached.
	 */
	public function assetUrl( string $file ): string
	{
		if ( ! $this->asset || ! is_file( $this->distPath( $file ) ) ) {
			return '';
		}

		return (string) call_user_func( $this->asset, $file );
	}

	/**
	 * Returns the stylesheet tag. Print this in the page head, before the
	 * theme's own stylesheet.
	 */
	public function styles(): string
	{
		$url = $this->assetUrl( 'chassis.css' );

		if ( ! $url ) {
			return '';
		}

		$html   = sprintf( '<link rel="stylesheet" href="%s">', $this->escape( $url ) );
		$design = $this->designCss();

		return $design ? $html . "\n" . sprintf( '<style id="chassis-design">%s</style>', $design ) : $html;
	}

	/**
	 * Sets the `chassis.json` files to read, parent theme first. Missing files
	 * are skipped. The service providers call this with the theme's files.
	 *
	 * @param string[] $files Paths to `chassis.json` files.
	 */
	public function designFiles( array $files ): self
	{
		$this->designFiles = array_values( array_filter( $files, 'is_string' ) );

		return $this;
	}

	/**
	 * Returns the merged settings from the `chassis.json` files.
	 */
	public function design(): array
	{
		$design = [];

		foreach ( array_unique( $this->designFiles ) as $file ) {
			if ( '' === $file || ! is_file( $file ) ) {
				continue;
			}

			$data = json_decode( (string) file_get_contents( $file ), true );

			if ( ! is_array( $data ) ) {
				trigger_error( sprintf( 'Chassis could not read %s: %s', $file, json_last_error_msg() ), E_USER_WARNING );
				continue;
			}

			$design = array_replace_recursive( $design, $data );
		}

		return $design;
	}

	/**
	 * Turns the `chassis.json` settings into custom properties:
	 *
	 * - `color.global.link` → `--color-link`
	 * - `color.header.text` → `--color-header-text` (any other group works the same)
	 * - `font.body` → `--font-family-body`
	 * - `spacing.content-padding` → `--content-padding`; size names such as
	 *   `l` or `xs m` become `var( --space-l )` and so on
	 * - `width.wide` → `--width-wide`
	 * - `design.radius` → `--radius`, for any other setting
	 * - `menu.toggle` → where the Menu button sits on phones: `bottom-left`
	 *   (the default), `bottom-right`, `top-right`, or `top-left`
	 *
	 * Returns an empty string when there's nothing to set.
	 */
	public function designCss( ?array $design = null ): string
	{
		$design     = $design ?? $this->design();
		$properties = [];

		foreach ( $this->group( $design, 'color' ) as $group => $colors ) {
			if ( ! is_array( $colors ) ) {
				continue;
			}

			$prefix = 'global' === $group ? 'color-' : "color-{$group}-";

			foreach ( $colors as $name => $value ) {
				$properties[ $prefix . $name ] = $value;
			}
		}

		foreach ( $this->group( $design, 'font' ) as $name => $value ) {
			$properties[ "font-family-{$name}" ] = $value;
		}

		foreach ( $this->group( $design, 'spacing' ) as $name => $value ) {
			$properties[ $name ] = is_string( $value ) ? $this->spaceValue( $value ) : $value;
		}

		foreach ( $this->group( $design, 'width' ) as $name => $value ) {
			$properties[ "width-{$name}" ] = $value;
		}

		$toggle = $this->group( $design, 'menu' )['toggle'] ?? null;

		if ( null !== $toggle ) {
			if ( is_string( $toggle ) && isset( self::MENU_TOGGLE[ $toggle ] ) ) {
				$properties = array_merge( $properties, self::MENU_TOGGLE[ $toggle ] );
			} else {
				trigger_error( sprintf( 'Chassis skipped menu.toggle in chassis.json: use %s.', implode( ', ', array_keys( self::MENU_TOGGLE ) ) ), E_USER_WARNING );
			}
		}

		// Last, so a theme's own settings win over the `menu.toggle` presets.
		foreach ( $this->group( $design, 'design' ) as $name => $value ) {
			$properties[ $name ] = $value;
		}

		$css = '';

		foreach ( $properties as $name => $value ) {
			$name = (string) $name;

			if ( ! preg_match( '/^[a-z0-9]+(-[a-z0-9]+)*$/', $name ) ) {
				trigger_error( sprintf( 'Chassis skipped the setting "%s" in chassis.json: names use lowercase letters, numbers, and dashes.', $name ), E_USER_WARNING );
				continue;
			}

			if ( ! is_string( $value ) && ! is_int( $value ) && ! is_float( $value ) ) {
				continue;
			}

			$value = trim( (string) $value );

			if ( '' === $value || preg_match( '/[{}<>;\\\\]|\/\*/', $value ) ) {
				trigger_error( sprintf( 'Chassis skipped the setting "%s" in chassis.json: its value isn\'t a plain CSS value.', $name ), E_USER_WARNING );
				continue;
			}

			$css .= "--{$name}:{$value};";
		}

		return $css ? ":root{{$css}}" : '';
	}

	/**
	 * Returns one top-level group from the settings, or an empty array.
	 */
	protected function group( array $design, string $key ): array
	{
		return isset( $design[ $key ] ) && is_array( $design[ $key ] ) ? $design[ $key ] : [];
	}

	/**
	 * Turns spacing size names into custom properties: `xs m` becomes
	 * `var( --space-xs ) var( --space-m )`. Other values are kept as they are.
	 */
	protected function spaceValue( string $value ): string
	{
		$parts = preg_split( '/\s+/', trim( $value ) );

		foreach ( $parts as &$part ) {
			if ( in_array( $part, self::SPACE_NAMES, true ) ) {
				$part = "var( --space-{$part} )";
			}
		}

		return implode( ' ', $parts );
	}

	/**
	 * Returns the script tag. Print this at the end of the page.
	 */
	public function scripts(): string
	{
		$url = $this->assetUrl( 'chassis.js' );

		return $url ? sprintf( '<script src="%s"></script>', $this->escape( $url ) ) : '';
	}

	/**
	 * Returns the path to the package's JavaScript (e.g. `js/app-nav.js`).
	 */
	public function jsPath( string $append = '' ): string
	{
		return $this->path( 'js' . ( $append ? '/' . ltrim( $append, '/' ) : '' ) );
	}

	/**
	 * Returns a badge, e.g. `badge( 'Flagship Theme', [ 'large' ] )`.
	 *
	 * @param string   $text      Label text.
	 * @param string[] $modifiers Modifiers such as `large`.
	 */
	public function badge( string $text, array $modifiers = [] ): string
	{
		return sprintf(
			'<span class="%s">%s</span>',
			$this->classes( 'badge', $modifiers ),
			$this->escape( $text )
		);
	}

	/**
	 * Returns a button-styled link, e.g. `button( 'Download', $url, [ 'primary' ] )`.
	 *
	 * @param string   $text      Link text.
	 * @param string   $url       Link URL.
	 * @param string[] $modifiers Modifiers such as `primary`.
	 */
	public function button( string $text, string $url, array $modifiers = [] ): string
	{
		return sprintf(
			'<a class="%s" href="%s">%s</a>',
			$this->classes( 'button', $modifiers ),
			$this->escape( $url ),
			$this->escape( $text )
		);
	}

	/**
	 * Builds a BEM class list: `button` + [ 'primary' ] → `button button--primary`.
	 *
	 * @param string   $block     Block class.
	 * @param string[] $modifiers Modifier names.
	 */
	public function classes( string $block, array $modifiers = [] ): string
	{
		$classes = [ $block ];

		foreach ( $modifiers as $modifier ) {
			$modifier = preg_replace( '/[^a-z0-9-]/', '', strtolower( (string) $modifier ) );

			if ( '' !== $modifier ) {
				$classes[] = "{$block}--{$modifier}";
			}
		}

		return implode( ' ', array_unique( $classes ) );
	}

	/**
	 * Escapes a value for HTML text or an attribute.
	 */
	protected function escape( string $value ): string
	{
		return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
