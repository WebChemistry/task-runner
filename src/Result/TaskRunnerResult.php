<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Result;

final class TaskRunnerResult
{

	/**
	 * @param TaskResult[] $results
	 */
	public function __construct(
		public readonly array $results = [],
	)
	{
	}

	public function hasException(): bool
	{
		foreach ($this->results as $run) {
			if ($run->exception) {
				return true;
			}
		}

		return false;
	}

	public function isSuccess(): bool
	{
		foreach ($this->results as $run) {
			if (!$run->success) {
				return false;
			}
		}

		return true;
	}

}
