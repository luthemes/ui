<?php
/**
 * Button template tag, for Novaris views.
 *
 *     <?= $engine->button( 'Read more', $url, [ 'primary' ] ) ?>
 *
 * @package Techkodeninja\Chassis
 */

namespace Techkodeninja\Chassis\Tags;

use Novaris\Template\Tag\Tag;

class Button extends Tag
{
	public function __construct(
		protected string $label,
		protected string $url,
		protected array $modifiers = []
	) {}

	public function toHtml(): string
	{
		return app( 'chassis' )->button( $this->label, $this->url, $this->modifiers );
	}

	public function toText(): string
	{
		return $this->label;
	}
}
