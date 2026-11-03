<?php
/**
 * Luthemes UI.
 *
 * Framework-agnostic helpers for the shared UI: where the package lives (so
 * build tools can find the SCSS) and small functions that print the markup
 * for its components. Works the same in ClassicPress themes (Backdrop) and
 * Novaris sites; each framework gets a thin service provider in `Providers/`.
 *
 * @package   Luthemes\UI
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2026 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/luthemes/ui
 */

namespace Luthemes\UI;

class UI
{
	/**
	 * Package version.
	 */
	const VERSION = '0.1.0';

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
