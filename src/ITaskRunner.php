<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use Psr\Log\LoggerInterface;
use WebChemistry\TaskRunner\Extension\ITaskRunnerExtension;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;

interface ITaskRunner
{

	public function addExtension(ITaskRunnerExtension $extension): static;

	public function removeExtension(ITaskRunnerExtension $extension): static;

	public function withExtension(ITaskRunnerExtension $extension): static;

	public function withLogger(LoggerInterface $logger): static;

	/**
	 * @return ITask[]
	 */
	public function getTasks(?string $id = null): array;

	/**
	 * @param string $id class name or schedule or task name
	 */
	public function run(string $id): TaskRunnerResult;

}
