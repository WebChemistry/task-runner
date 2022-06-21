<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Task
{

	public function __construct(
		public ?string $name = null,
		public ?string $group = null,
	)
	{
	}

}
