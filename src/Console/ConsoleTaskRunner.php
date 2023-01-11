<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Console;

use DomainException;
use WebChemistry\TaskRunner\ITaskRunner;

final class ConsoleTaskRunner
{

	public static function boot(ITaskRunner $taskRunner, ?string $memoryLimit = '-1', ?int $executionTime = 120): void
	{
		if ($memoryLimit || $executionTime) {
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1');
			error_reporting(E_ALL);

			if ($memoryLimit) {
				ini_set('memory_limit', $memoryLimit);
			}

			if ($executionTime) {
				ini_set('max_execution_time', (string) $executionTime);
			}
		}

		$id = $_SERVER['argv'][1] ?? null;

		if (!$id) {
			throw new DomainException('Please give id.');
		}

		$result = $taskRunner->run($id);

		if (!$result->isSuccess()) {
			exit(1);
		}
	}

}
