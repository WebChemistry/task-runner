<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Result;

use Throwable;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\Logger\TaskLogger;

final class TaskResult
{

	public function __construct(
		public ITask $task,
		public TaskLogger $logger,
		public bool $success = true,
		public ?Throwable $error = null,
	)
	{
	}

}
