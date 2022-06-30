<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use WebChemistry\TaskRunner\Extension\ITaskRunnerExtension;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;

interface ITaskRunner
{

	public function addExtension(ITaskRunnerExtension $extension): static;

	public function removeExtension(ITaskRunnerExtension $extension): static;

	public function withExtension(ITaskRunnerExtension $extension): static;

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
