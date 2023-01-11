<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Utility;

use WebChemistry\TaskRunner\IScheduledTask;
use WebChemistry\TaskRunner\ITask;

final class TaskSimpleGrouper
{

	/**
	 * @param ITask[] $tasks
	 * @return array<string, ITask[]>
	 */
	public static function groups(array $tasks): array
	{
		$groups = [];
		$empty = [];

		foreach ($tasks as $task) {
			if (!$task instanceof IScheduledTask) {
				$empty[] = $task;

				continue;
			}

			$id = $task->getId() ?? TaskIdentifier::extractId($task::class);

			$groups[$id] ??= [];
			$groups[$id][] = $task;
		}

		if ($empty) {
			$groups[''] = $empty;
		}

		return $groups;
	}

}
