<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Console;

use OutOfBoundsException;
use WebChemistry\TaskRunner\ITaskRunner;

final class ConsoleTaskRunner
{

	public static function boot(ITaskRunner $taskRunner): void
	{
		self::initialize();

		$name = $_SERVER['argv'][1] ?? null;
		if (!$name) {
			throw new OutOfBoundsException('Please fill task name or class/interface name.');
		}

		$taskRunner->run(strtr($name, ['/' => '\\']));
	}

	private static function initialize(): void
	{
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		ini_set('memory_limit', '-1');

		error_reporting(E_ALL);
	}

}
