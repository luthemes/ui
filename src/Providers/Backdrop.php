<?php
/**
 * Backdrop service provider, for ClassicPress themes.
 *
 * Register in the theme's `app/framework.php`:
 *
 *     $theme->provider( Techkodeninja\Chassis\Providers\Backdrop::class );
 *
 * This is what turns Chassis on. It loads the CSS and JavaScript for you, and
 * `Backdrop\App::resolve( 'chassis' )` gives you the helpers.
 *
 * Requires backdrop-dev/framework, which the theme already has.
 *
 * @package Techkodeninja\Chassis
 */

namespace Techkodeninja\Chassis\Providers;

use Backdrop\Core\ServiceProvider;
use Techkodeninja\Chassis\Chassis;

class Backdrop extends ServiceProvider
{
	/**
	 * Binds the UI helpers to the container, and records the platform.
	 */
	public function register(): void
	{
		$this->app->singleton( Chassis::class, function() {
			return new Chassis( 'backdrop' );
		} );
		$this->app->alias( Chassis::class, 'chassis' );
	}
}
