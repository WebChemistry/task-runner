<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use Nette\SmartObject;
use Throwable;

final class TaskRunner implements ITaskRunner
{

	use SmartObject;

	/**
	 * @param ITask[] $tasks
	 */
	public function __construct(
		private array $tasks
	)
	{
	}

	public function run(string $instanceOf): void
	{
		$success = true;
		foreach ($this->tasks as $task) {
			if ($task instanceof $instanceOf) {
				$state = $this->runTask($task);
				if ($state === false) {
					$success = false;
				}
			}
		}

		if (!$success) {
			exit(1);
		}
	}

	private function runTask(ITask $task): bool
	{
		$name = get_class($task);
		$success = true;
		echo "Task $name started\n";

		try {
			$state = $task->run();
			if ($state === false) {
				$success = false;
			}
		} catch (Throwable $exception) {
			echo "Task $name errored\n";
			echo (string) $exception . "\n";

			$success = false;
		}

		if ($success) {
			echo "Task $name success\n";
		}

		return $success;
	}

}
