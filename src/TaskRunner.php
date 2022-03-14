<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use OutOfBoundsException;
use ReflectionClass;
use Throwable;
use WebChemistry\TaskRunner\Attribute\Task;

final class TaskRunner implements ITaskRunner
{

	/**
	 * @param ITask[] $tasks
	 */
	public function __construct(
		private array $tasks
	)
	{
	}

	public function runByName(string $name): void
	{
		$task = $this->getByName($name);

		if ($task === null) {
			throw new OutOfBoundsException(sprintf('Task with %s does not exist.', $name));
		}

		$task->run();
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

	private function getByName(string $name): ?ITask
	{
		foreach ($this->tasks as $task) {
			$reflection = new ReflectionClass($task);
			foreach ($reflection->getAttributes(Task::class) as $attribute) {
				/** @var Task $instance */
				$instance = $attribute->newInstance();

				if ($instance->name === $name) {
					return $task;
				}
			}
		}

		return null;
	}

}
