<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Console;

use DomainException;
use OutOfBoundsException;
use Typertion\Php\TypeAssert;
use WebChemistry\TaskRunner\ITask;
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

		$name = $_SERVER['argv'][1] ?? null;
		$arg = $_SERVER['argv'][2] ?? null;

		if (!in_array($name, ['class', 'name', 'group'], true)) {
			throw new OutOfBoundsException('First argument must be class or name or group.');
		}

		if (!$arg) {
			throw new DomainException('Second argument must be a string.');
		}

		$result = match ($name) {
			'class' => $taskRunner->run(TypeAssert::classStringOf(strtr($arg, ['/' => '\\']), ITask::class)),
			'name' => $taskRunner->runByName($arg),
			'group' => $taskRunner->runByGroup($arg),
		};

		if (!$result->isSuccess()) {
			exit(1);
		}
	}

}
