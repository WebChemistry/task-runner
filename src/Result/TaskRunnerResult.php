<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Result;

use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\Printer\DefaultPrinter;

final class TaskRunnerResult
{

	/**
	 * @param TaskResult[] $run
	 */
	public function __construct(
		public array $run = [],
	)
	{
	}

	public function hasException(): bool
	{
		foreach ($this->run as $run) {
			if ($run->error) {
				return true;
			}
		}

		return false;
	}

	public function isSuccess(): bool
	{
		foreach ($this->run as $run) {
			if (!$run->success) {
				return false;
			}
		}

		return true;
	}

	public function print(bool $exit = false): void
	{
		(new DefaultPrinter($this))->print($exit);
	}

}
