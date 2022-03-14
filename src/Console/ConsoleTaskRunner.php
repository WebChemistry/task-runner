<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Console;

use OutOfBoundsException;
use WebChemistry\TaskRunner\ITaskRunner;

final class ConsoleTaskRunner
{

	public static function bootByName(ITaskRunner $taskRunner): void
	{
		$name = $_SERVER['argv'][1] ?? null;
		if (!$name) {
			throw new OutOfBoundsException('Please fill task name.');
		}

		$taskRunner->runByName($name);
	}

}
