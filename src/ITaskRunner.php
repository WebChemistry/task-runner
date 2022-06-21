<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use WebChemistry\TaskRunner\Result\TaskRunnerResult;

interface ITaskRunner
{

	/**
	 * @return ITask[]
	 */
	public function getTasks(): array;

	public function runByGroup(string $group): TaskRunnerResult;

	public function runByName(string $name): TaskRunnerResult;

	/**
	 * @param class-string<ITask> $className
	 */
	public function run(string $className): TaskRunnerResult;

}
