<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Result;

use Throwable;
use WebChemistry\TaskRunner\ITask;

final class TaskResult
{

	public function __construct(
		public readonly ITask $task,
		public readonly bool $success = true,
		public readonly ?Throwable $exception = null,
	)
	{
	}

	public function hasException(): bool
	{
		return (bool) $this->exception;
	}

}
