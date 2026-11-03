<?php
/**
 * Novaris service provider.
 *
 * Register in a Novaris site's `config/app.php`:
 *
 *     'providers' => [ Luthemes\UI\Providers\Novaris::class ],
 *
 * Then use it anywhere with `app( 'luthemes.ui' )`.
 *
 * Requires novaris-dev/framework, which the site already has.
 *
 * @package Luthemes\UI
 */

namespace Luthemes\UI\Providers;

use Luthemes\UI\UI;
use Novaris\Core\ServiceProvider;

class Novaris extends ServiceProvider
{
	/**
	 * Binds the UI helpers to the container.
	 */
	public function register(): void
	{
		$this->app->singleton( UI::class );
		$this->app->alias( UI::class, 'luthemes.ui' );
	}
}
