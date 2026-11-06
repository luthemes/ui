<?php
/**
 * Publisher.
 *
 * Copies Chassis's built files from `dist/` into a folder the browser can
 * reach, and returns their URLs. Each copy's name includes a hash of its
 * contents, so a new version gets a new URL and browsers don't keep an old
 * one. Older copies are removed.
 *
 * The Novaris provider uses this for `public/assets/chassis`, and
 * `Chassis::standalone()` uses it for any folder.
 *
 * @package Techkodeninja\Chassis
 */

namespace Techkodeninja\Chassis;

class Publisher
{
	/**
	 * Folder the files are copied into.
	 *
	 * @var string
	 */
	protected $folder;

	/**
	 * URL of that folder.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * @param string $folder Folder the browser can reach, e.g. `__DIR__ . '/public/chassis'`.
	 * @param string $url    Its URL, e.g. `/chassis`.
	 */
	public function __construct( string $folder, string $url )
	{
		$this->folder = rtrim( $folder, '/\\' );
		$this->url    = rtrim( $url, '/' );
	}

	/**
	 * Copies a file from `dist/` if needed, and returns its URL. Returns an
	 * empty string if it can't be copied.
	 */
	public function __invoke( string $file ): string
	{
		$source = dirname( __DIR__ ) . '/dist/' . ltrim( $file, '/' );

		if ( ! is_file( $source ) ) {
			return '';
		}

		$info   = pathinfo( $file );
		$name   = sprintf( '%s-%s.%s', $info['filename'], substr( md5_file( $source ), 0, 8 ), $info['extension'] );
		$target = $this->folder . '/' . $name;

		if ( ! is_file( $target ) ) {
			if ( ! $this->copy( $source, $target ) ) {
				return '';
			}

			$this->removeOldCopies( $info['filename'], $info['extension'], $name );
		}

		return $this->url . '/' . $name;
	}

	/**
	 * Copies a file, and logs a warning if that fails, so a missing stylesheet
	 * has a clear cause.
	 */
	protected function copy( string $source, string $target ): bool
	{
		if ( ( is_dir( $this->folder ) || @mkdir( $this->folder, 0755, true ) ) && @copy( $source, $target ) ) {
			return true;
		}

		trigger_error(
			sprintf( 'Chassis could not copy its files to %s. Check that the folder is writable.', $this->folder ),
			E_USER_WARNING
		);

		return false;
	}

	/**
	 * Removes earlier versions of a file. The pattern is exact, so `chassis.css`
	 * doesn't match `chassis-classicpress.css`.
	 */
	protected function removeOldCopies( string $filename, string $extension, string $keep ): void
	{
		$pattern = sprintf( '/^%s-[0-9a-f]{8}\.%s$/', preg_quote( $filename, '/' ), preg_quote( $extension, '/' ) );

		foreach ( scandir( $this->folder ) ?: [] as $file ) {
			if ( $file !== $keep && preg_match( $pattern, $file ) ) {
				@unlink( $this->folder . '/' . $file );
			}
		}
	}
}
