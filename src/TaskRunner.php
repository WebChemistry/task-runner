<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use OutOfBoundsException;
use ReflectionClass;
use Throwable;
use WebChemistry\TaskRunner\Attribute\Task;
use WebChemistry\TaskRunner\Printer\IPrinter;
use WebChemistry\TaskRunner\Printer\StdoutPrinter;

final class TaskRunner implements ITaskRunner
{

	private IPrinter $printer;

	/**
	 * @param ITask[] $tasks
	 */
	public function __construct(
		private array $tasks,
		IPrinter $printer = null,
	)
	{
		$this->printer = $printer ?? new StdoutPrinter();
	}

	public function runByName(string $name): void
	{
		$task = $this->getByName($name);

		if ($task === null) {
			throw new OutOfBoundsException(sprintf('Task with %s does not exist.', $name));
		}

		$task->run($this->printer);
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

		$this->printer->printStep(sprintf('Task %s started', $name));

		try {
			$state = $task->run();
			if ($state === false) {
				$success = false;
			}
		} catch (Throwable $exception) {
			$this->printer->printStep(sprintf('Task %s errored', $name));
			$this->printer->printError((string) $exception);

			$success = false;
		}

		if ($success) {
			$this->printer->printStep(sprintf('Task %s success', $name));
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
