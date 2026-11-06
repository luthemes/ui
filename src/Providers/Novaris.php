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
use Techkodeninja\Chassis\Publisher;
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
	 * reach `vendor`, and returns its URL. Returns an empty string if the file
	 * can't be copied.
	 */
	public function publish( string $file ): string
	{
		return ( new Publisher( public_path( 'assets/chassis' ), public_url( 'assets/chassis' ) ) )( $file );
	}
}
