<?php
/**
 * Backdrop service provider, for ClassicPress themes.
 *
 * Register in the theme's `app/framework.php`:
 *
 *     $theme->provider( Luthemes\UI\Providers\Backdrop::class );
 *
 * Then use it anywhere with `Backdrop\App::resolve( 'luthemes.ui' )`.
 *
 * Requires backdrop-dev/framework, which the theme already has.
 *
 * @package Luthemes\UI
 */

namespace Luthemes\UI\Providers;

use Backdrop\Core\ServiceProvider;
use Luthemes\UI\UI;

class Backdrop extends ServiceProvider
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
