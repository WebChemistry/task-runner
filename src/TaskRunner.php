<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use LogicException;
use Psr\Log\LoggerInterface;
use Throwable;
use WebChemistry\TaskRunner\Extension\ITaskRunnerExtension;
use WebChemistry\TaskRunner\Logger\StdoutLogger;
use WebChemistry\TaskRunner\Result\TaskResult;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;
use WebChemistry\TaskRunner\Utility\TaskRunnerUtility;

final class TaskRunner implements ITaskRunner
{

	/** @var ITaskRunnerExtension[] */
	private array $extensions = [];

	private LoggerInterface $logger;

	/**
	 * @param ITask[] $tasks
	 */
	public function __construct(
		private array $tasks,
		?LoggerInterface $logger = null,
	)
	{
		$this->logger = $logger ?? new StdoutLogger();
	}

	public function withExtension(ITaskRunnerExtension $extension): self
	{
		$cloned = clone $this;
		$cloned->extensions[] = $extension;

		return $cloned;
	}

	public function addExtension(ITaskRunnerExtension $extension): self
	{
		$this->extensions[] = $extension;

		return $this;
	}

	public function removeExtension(ITaskRunnerExtension $extension): self
	{
		if (($key = array_search($extension, $this->extensions, true)) !== false) {
			unset($this->extensions[$key]);
		}

		return $this;
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
			$result->results[] = $this->runTask($task);
		}

		return $result;
	}

	private function runTask(ITask $task): TaskResult
	{
		foreach ($this->extensions as $extension) {
			$extension->beforeTask($task, $this->logger);
		}

		try {
			$success = $task->run($this->logger);

			$result = new TaskResult($task, is_bool($success) ? $success : true);
		} catch (Throwable $exception) {
			$result = new TaskResult($task, false, $exception);
		}

		foreach ($this->extensions as $extension) {
			$extension->afterTask($task, $result, $this->logger);
		}

		return $result;
	}

}
