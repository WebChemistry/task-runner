<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Utility;

use DomainException;
use ReflectionClass;
use WebChemistry\TaskRunner\Attribute\Task;
use WebChemistry\TaskRunner\ITask;

final class TaskRunnerUtility
{

	/**
	 * @param ITask[] $tasks
	 * @return array<string, ITask>
	 */
	public static function getTaskNames(array $tasks): array
	{
		$return = [];

		foreach ($tasks as $task) {
			$name = self::getTaskName($task);

			if ($name === null) {
				continue;
			}

			if (isset($return[$name])) {
				throw new DomainException(
					sprintf(
						'Found two tasks with same name, %s and %s.',
						$return[$name]::class,
						$task::class,
					)
				);
			}

			$return[$name] = $task;
		}

		return $return;
	}

	public static function getTaskName(ITask $task): ?string
	{
		$reflection = new ReflectionClass($task);
		foreach ($reflection->getAttributes(Task::class) as $attribute) {
			/** @var Task $instance */
			$instance = $attribute->newInstance();

			if (!$instance->name) {
				continue;
			}

			return $instance->name;
		}

		return null;
	}

	/**
	 * @param ITask[] $tasks
	 * @return array<string, ITask[]>
	 */
	public static function getTaskByGroups(array $tasks): array
	{
		$return = [];

		foreach ($tasks as $task) {
			foreach (self::getTaskGroups($task) as $group) {
				if (!isset($return[$group])) {
					$return[$group] = [];
				}

				$return[$group][] = $task;
			}
		}

		return $return;
	}

	/**
	 * @return string[]
	 */
	public static function getTaskGroups(ITask $task): array
	{
		$reflection = new ReflectionClass($task);
		$groups = [];

		$attributes = $reflection->getAttributes(Task::class);

		foreach ($reflection->getInterfaces() as $interface) {
			$attributes = array_merge($attributes, $interface->getAttributes(Task::class));
		}

		foreach ($attributes as $attribute) {
			/** @var Task $instance */
			$instance = $attribute->newInstance();

			if ($instance->group === null) {
				continue;
			}

			$groups[] = $instance->group;
		}

		return array_unique($groups);
	}

}
