<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Export\Group;

use DateTimeZone;
use Exception;
use WebChemistry\TaskRunner\Attribute\Schedule;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Utility\TaskIdentifier;

final class TaskGroup
{

	/** @var ITask[] */
	private array $tasks = [];

	public function __construct(
		private string $id,
		private Schedule $schedule,
		private ITaskRunner $taskRunner,
	)
	{
	}

	/**
	 * @return ITask[]
	 */
	public function getTasks(): array
	{
		return $this->tasks;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getSchedule(): Schedule
	{
		return $this->schedule;
	}

	public function getTimeZone(): ?DateTimeZone
	{
		return $this->timeZone;
	}

	public function addTask(ITask $task): void
	{
		$id = $task->getId() ?? TaskIdentifier::extractId($task::class);
		$schedule = $task->getSchedule();

		if (!$this->schedule->isEqual($schedule)) {
			throw new Exception(
				sprintf(
					'Schedule %s from %s is not compatible with %s from %s.',
					$schedule->getNormalized(),
					$task::class,
					$this->schedule->getNormalized(),
					implode(' , ', array_map(
						fn (ITask $task) => $task::class,
						$this->tasks,
					)),
				),
			);
		}

		$this->tasks[] = $task;
	}

}
