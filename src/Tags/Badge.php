<?php
/**
 * Badge template tag, for Novaris views.
 *
 *     <?= $engine->badge( 'Featured' ) ?>
 *
 * @package Techkodeninja\Chassis
 */

namespace Techkodeninja\Chassis\Tags;

use Novaris\Template\Tag\Tag;

class Badge extends Tag
{
	public function __construct(
		protected string $label,
		protected array $modifiers = []
	) {}

	public function toHtml(): string
	{
		return app( 'chassis' )->badge( $this->label, $this->modifiers );
	}

	public function toText(): string
	{
		return $this->label;
	}
}
