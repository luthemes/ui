<?php
/**
 * Novaris service provider.
 *
 * Register in a Novaris site's `config/app.php`:
 *
 *     'providers' => [ Techkodeninja\Chassis\Providers\Novaris::class ],
 *
 * This is what turns Chassis on. Without it, the CSS and JavaScript don't load.
 * Print them in the theme's views:
 *
 *     <?= app( 'chassis' )->styles() ?>   // in the head
 *     <?= app( 'chassis' )->scripts() ?>  // at the end of the page
 *
 * The theme's `chassis.json` sets its colors, fonts, and spacing, and
 * `styles()` prints them after the stylesheet.
 *
 * Requires novaris-dev/framework, which the site already has.
 *
 * @package Techkodeninja\Chassis
 */

namespace Techkodeninja\Chassis\Providers;

use Techkodeninja\Chassis\Chassis;
use Novaris\Core\ServiceProvider;
use Techkodeninja\Chassis\Tags\{Badge, Button};

class Novaris extends ServiceProvider
{
	/**
	 * Binds the UI helpers to the container, and records the platform.
	 */
	public function register(): void
	{
		$this->app->singleton( Chassis::class, function() {
			$chassis = new Chassis( 'novaris', [ $this, 'publish' ] );

			return $chassis->designFiles( $this->designFiles() );
		} );
		$this->app->alias( Chassis::class, 'chassis' );
	}

	/**
	 * Adds the template tags, so views can use `$engine->button()`.
	 */
	public function boot(): void
	{
		$tags = $this->app->make( 'template.tags' );

		$tags->add( 'badge', Badge::class );
		$tags->add( 'button', Button::class );
	}

	/**
	 * Returns the theme's `chassis.json` files, the parent theme's first so
	 * the active theme overrides it.
	 *
	 * @return string[]
	 */
	protected function designFiles(): array
	{
		$parent = function_exists( 'parent_theme_path' ) ? parent_theme_path() : '';

		return array_filter( [
			$parent ? $parent . '/chassis.json' : '',
			theme_path( 'chassis.json' ),
		] );
	}

	/**
	 * Copies a built file into `public/assets/chassis`, because browsers can't
	 * reach `vendor`, and returns its URL. The name includes a hash of the
	 * contents, so a new version of the file gets a new URL. Returns an empty
	 * string if the file can't be copied.
	 */
	public function publish( string $file ): string
	{
		$source = $this->app->make( Chassis::class )->distPath( $file );
		$info   = pathinfo( $file );
		$name   = sprintf( '%s-%s.%s', $info['filename'], substr( md5_file( $source ), 0, 8 ), $info['extension'] );
		$folder = public_path( 'assets/chassis' );
		$target = $folder . '/' . $name;

		if ( ! is_file( $target ) ) {
			if ( ! $this->copy( $source, $folder, $target ) ) {
				return '';
			}

			$this->removeOldCopies( $folder, $info['filename'], $info['extension'], $name );
		}

		return public_url( 'assets/chassis/' . $name );
	}

	/**
	 * Copies a file, and logs a warning if that fails, so a missing stylesheet
	 * has a clear cause.
	 */
	protected function copy( string $source, string $folder, string $target ): bool
	{
		if ( ( is_dir( $folder ) || @mkdir( $folder, 0755, true ) ) && @copy( $source, $target ) ) {
			return true;
		}

		trigger_error(
			sprintf( 'Chassis could not copy its files to %s. Check that the folder is writable.', $folder ),
			E_USER_WARNING
		);

		return false;
	}

	/**
	 * Removes earlier versions of a file. The pattern is exact, so `chassis.css`
	 * doesn't match `chassis-classicpress.css`.
	 */
	protected function removeOldCopies( string $folder, string $filename, string $extension, string $keep ): void
	{
		$pattern = sprintf( '/^%s-[0-9a-f]{8}\.%s$/', preg_quote( $filename, '/' ), preg_quote( $extension, '/' ) );

		foreach ( scandir( $folder ) ?: [] as $file ) {
			if ( $file !== $keep && preg_match( $pattern, $file ) ) {
				@unlink( $folder . '/' . $file );
			}
		}
	}
}
