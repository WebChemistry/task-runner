<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Extension;

use Psr\Log\LoggerInterface;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\Result\TaskResult;

interface ITaskRunnerExtension
{

	public function beforeTask(ITask $task, LoggerInterface $logger): void;

	public function afterTask(ITask $task, TaskResult $result, LoggerInterface $logger): void;

}
