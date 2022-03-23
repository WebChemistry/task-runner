<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use Generator;
use LogicException;
use ReflectionClass;
use Throwable;
use WebChemistry\TaskRunner\Attribute\Task;
use WebChemistry\TaskRunner\Printer\ConsolePrinter;
use WebChemistry\TaskRunner\Printer\IPrinter;

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
		$this->printer = $printer ?? new ConsolePrinter();
	}

	/**
	 * @template T
	 * @param class-string<T> $class
	 * @return Generator<T>
	 */
	private function getTasksByClassName(string $class): Generator
	{
		foreach ($this->tasks as $task) {
			if ($task instanceof $class) {
				yield $task;
			}
		}
	}

	/**
	 * @return Generator<ITask>
	 */
	private function getTasksByName(string $name): Generator
	{
		foreach ($this->tasks as $task) {
			$reflection = new ReflectionClass($task);
			foreach ($reflection->getAttributes(Task::class) as $attribute) {
				/** @var Task $instance */
				$instance = $attribute->newInstance();

				if ($instance->name === $name) {
					yield $task;
				}
			}
		}
	}

	public function run(string $name): void
	{
		if (class_exists($name) || interface_exists($name)) {
			$tasks = iterator_to_array($this->getTasksByClassName($name));

			if (!$tasks) {
				throw new LogicException(sprintf('No tasks found by %s', $name));
			}
		} else {
			$tasks = iterator_to_array($this->getTasksByName($name));

			if (!$tasks) {
				throw new LogicException(sprintf('No tasks found by %s', $name));
			}
		}

		$success = true;
		foreach ($tasks as $task) {
			$state = $this->runTask($task);

			if ($state === false) {
				$success = false;
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
			$state = $task->run($this->printer);
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

}
