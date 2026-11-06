<?php
/**
 * Novaris service provider.
 *
 * Register in a Novaris site's `config/app.php`:
 *
 *     'providers' => [ Techkodeninja\Chassis\Providers\Novaris::class ],
 *
 * Then use it anywhere with `app( 'chassis' )`.
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
			return new Chassis( 'novaris' );
		} );
		$this->app->alias( Chassis::class, 'chassis' );
	}
}
