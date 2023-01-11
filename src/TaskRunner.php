<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use LogicException;
use Psr\Log\LoggerInterface;
use Throwable;
use WebChemistry\TaskRunner\Extension\ITaskRunnerExtension;
use WebChemistry\TaskRunner\Logger\StdoutLogger;
use WebChemistry\TaskRunner\Result\TaskResult;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;

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

	public function withExtension(ITaskRunnerExtension $extension): static
	{
		$cloned = clone $this;
		$cloned->extensions[] = $extension;

		return $cloned;
	}

	public function withLogger(LoggerInterface $logger): static
	{
		$clone = clone $this;
		$clone->logger = $logger;

		return $clone;
	}

	public function addExtension(ITaskRunnerExtension $extension): static
	{
		$this->extensions[] = $extension;

		return $this;
	}

	public function removeExtension(ITaskRunnerExtension $extension): static
	{
		if (($key = array_search($extension, $this->extensions, true)) !== false) {
			unset($this->extensions[$key]);
		}

		return $this;
	}

	/**
	 * @return ITask[]
	 */
	public function getTasks(?string $id = null): array
	{
		if ($id === null) {
			return $this->tasks;
		}

		$tasks = [];

		foreach ($this->tasks as $task) {
			if ($task->getId() === $id || $this->equalSchedule($id, $task) || $this->equalClassNames($id, $task::class)) {
				$tasks[] = $task;
			}
		}

		return $tasks;
	}

	/**
	 * @param string $id class name or schedule or task name
	 */
	public function run(string $id): TaskRunnerResult
	{
		$tasks = $this->getTasks($id);

		if (!$tasks) {
			throw new LogicException(sprintf('No tasks found by %s.', $id));
		}

		return $this->runTasks($tasks);
	}

	private function equalSchedule(string $schedule, ITask $task): bool
	{
		if (!$task instanceof IScheduledTask) {
			return false;
		}

		return $task->getSchedule()->getNormalized() === $schedule;
	}

	private function equalClassNames(string $first, string $second): bool
	{
		$first = strtr($first, ['\\' => '/']);
		$second = strtr($second, ['\\' => '/']);

		return strcasecmp($first, $second) === 0;
	}

	/**
	 * @param ITask[] $tasks
	 */
	private function runTasks(array $tasks): TaskRunnerResult
	{
		$results = [];

		foreach ($tasks as $task) {
			$results[] = $this->runTask($task);
		}

		return new TaskRunnerResult($results);
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
