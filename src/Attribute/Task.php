<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Attribute;

use Attribute;
use DateTimeZone;

#[Attribute(Attribute::TARGET_CLASS)]
final class Task
{

	public function __construct(
		public ?string $name = null,
		public ?string $group = null,
		public ?string $task = null,
		public ?string $description = null,
		public bool $export = true,
		public ?DateTimeZone $timeZone = null,
		public ?ISchedule $schedule = null,
	)
	{
	}

}
