<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Extension;

use Psr\Log\LoggerInterface;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\Result\TaskResult;

final class ProgressPrinterExtension implements ITaskRunnerExtension
{

	public function beforeTask(ITask $task, LoggerInterface $logger): void
	{
		$logger->info(sprintf('Task %s is running.', $task::class));
	}

	public function afterTask(ITask $task, TaskResult $result, LoggerInterface $logger): void
	{
		if ($result->success) {
			$logger->log('success', sprintf('Task %s succeed.', $task::class));
		} elseif ($result->hasException()) {
			$logger->info(sprintf('Task %s thrown following exception:', $task::class));
			$logger->alert($result->exception);
		} else {
			$logger->error(sprintf('Task %s errored.', $task::class));
		}
	}

}
