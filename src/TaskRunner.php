<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use LogicException;
use Throwable;
use WebChemistry\TaskRunner\Logger\StackLogger;
use WebChemistry\TaskRunner\Result\TaskResult;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;
use WebChemistry\TaskRunner\Utility\TaskRunnerUtility;

final class TaskRunner implements ITaskRunner
{

	/**
	 * @param ITask[] $tasks
	 */
	public function __construct(
		private array $tasks,
	)
	{
	}

	/**
	 * @template T of ITask
	 * @param class-string<T> $class
	 * @return T[]
	 */
	private function getTasksByClassName(string $class): array
	{
		$return = [];

		foreach ($this->tasks as $task) {
			if ($task instanceof $class) {
				$return[] = $task;
			}
		}

		return $return;
	}

	/**
	 * @return ITask[]
	 */
	public function getTasks(): array
	{
		return $this->tasks;
	}

	public function runByGroup(string $group): TaskRunnerResult
	{
		$tasks = TaskRunnerUtility::getTaskByGroups($this->tasks)[$group] ?? [];

		if (!$tasks) {
			throw new LogicException(sprintf('No task grouped as %s', $group));
		}

		return $this->runTasks($tasks);
	}

	public function runByName(string $name): TaskRunnerResult
	{
		$task = TaskRunnerUtility::getTaskNames($this->tasks)[$name] ?? throw new LogicException(sprintf('No task named as %s', $name));

		return $this->runTasks([$task]);
	}

	/**
	 * @param class-string<ITask> $className
	 */
	public function run(string $className): TaskRunnerResult
	{
		$tasks = $this->getTasksByClassName($className);

		if (!$tasks) {
			throw new LogicException(sprintf('No tasks found by %s', $className));
		}

		return $this->runTasks($tasks);
	}

	/**
	 * @param ITask[] $tasks
	 */
	private function runTasks(array $tasks): TaskRunnerResult
	{
		$result = new TaskRunnerResult();

		foreach ($tasks as $task) {
			$result->run[] = $this->runTask($task);
		}

		return $result;
	}

	private function runTask(ITask $task): TaskResult
	{
		$result = new TaskResult($task, $logger = new StackLogger());

		try {
			$success = $task->run($logger);

			$result->success = is_bool($success) ? $success : true;
		} catch (Throwable $exception) {
			$result->error = $exception;
			$result->success = false;
		}

		return $result;
	}

}
