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
 * Requires novaris-dev/framework, which the site already has.
 *
 * @package Techkodeninja\Chassis
 */

namespace Techkodeninja\Chassis\Providers;

use Techkodeninja\Chassis\Chassis;
use Novaris\Core\ServiceProvider;

class Novaris extends ServiceProvider
{
	/**
	 * Binds the UI helpers to the container, and records the platform.
	 */
	public function register(): void
	{
		$this->app->singleton( Chassis::class, function() {
			return new Chassis( 'novaris', [ $this, 'publish' ] );
		} );
		$this->app->alias( Chassis::class, 'chassis' );
	}

	/**
	 * Copies a built file into `public/assets/chassis`, because browsers can't
	 * reach `vendor`, and returns its URL. The name includes a hash of the
	 * contents, so a new version of the file gets a new URL.
	 */
	public function publish( string $file ): string
	{
		$source = $this->app->make( Chassis::class )->distPath( $file );
		$info   = pathinfo( $file );
		$name   = sprintf( '%s-%s.%s', $info['filename'], substr( md5_file( $source ), 0, 8 ), $info['extension'] );
		$target = public_path( 'assets/chassis/' . $name );

		if ( ! is_file( $target ) ) {
			if ( ! is_dir( dirname( $target ) ) ) {
				mkdir( dirname( $target ), 0755, true );
			}

			copy( $source, $target );
		}

		return public_url( 'assets/chassis/' . $name );
	}
}
