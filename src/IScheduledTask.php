<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use WebChemistry\TaskRunner\Attribute\Schedule;

interface IScheduledTask
{

	public function getSchedule(): Schedule;

}
