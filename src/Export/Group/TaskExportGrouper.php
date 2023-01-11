<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Export\Group;

use WebChemistry\TaskRunner\IScheduledTask;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Utility\TaskIdentifier;

final class TaskExportGrouper
{

	public function __construct(
		private ITaskRunner $taskRunner,
	)
	{
	}

	/**
	 * @return TaskGroup[]
	 */
	public function getGroups(): array
	{
		$groups = [];

		foreach ($this->taskRunner->getTasks() as $task) {
			if (!$task instanceof IScheduledTask) {
				continue;
			}

			$id = $task->getId() ?? TaskIdentifier::extractId($task::class);

			($groups[$id] ??= new TaskGroup($id, $task->getSchedule(), $this->taskRunner))
				->addTask($task);
		}

		return $groups;
	}

}
