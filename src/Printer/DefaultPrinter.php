<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Printer;

use Throwable;
use WebChemistry\TaskRunner\Logger\Severity;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;

final class DefaultPrinter
{

	private const STEP = 36;
	private const SUCCESS = 32;
	private const WARNING = 33;
	private const ERROR = 31;

	public function __construct(
		private readonly TaskRunnerResult $result,
	)
	{
	}

	public function print(bool $exit = false): void
	{
		$succeeded = $errored = [];

		foreach ($this->result->run as $run) {
			if ($run->success) {
				$succeeded[] = $run;
			} else {
				$errored[] = $run;
			}
		}

		$summaryStack = [];

		foreach ($succeeded as $result) {
			$stack = $result->logger?->getStack();

			if ($stack) {
				$this->printColor(sprintf('Task %s successed and had following log:', $result->task::class), self::SUCCESS);

				$this->printStack($stack);

				if (is_string($summary = $stack[2]['summary'] ?? null)) {
					$summaryStack[$summary] ??= 0;
					$summaryStack[$summary]++;
				}

			} else {
				$this->printColor(sprintf('Task %s successed.', $result->task::class), self::SUCCESS);
			}
		}

		foreach ($errored as $result) {
			$stack = $result->logger?->getStack();

			if ($stack) {
				$this->printColor(sprintf('Task %s errored and had following log:', $result->task::class), self::ERROR);

				$this->printStack($stack);

				if (is_string($summary = $stack[2]['summary'] ?? null)) {
					$summaryStack[$summary] ??= 0;
					$summaryStack[$summary]++;
				}

			} else {
				$this->printColor(sprintf('Task %s errored.', $result->task::class), self::ERROR);
			}

			if ($error = $result->error) {
				$this->printException($error);
			}
		}

		$this->printSummary($summaryStack);

		if ($exit && $errored) {
			exit($this->result->hasException() ? 255 : 1);
		}
	}

	/**
	 * @param array{mixed, string, mixed[]}[] $stack
	 */
	private function printStack(array $stack): void
	{
		foreach ($stack as [$type, $content]) {
			$this->printColor($content, match ($type) {
				Severity::EMERGENCY, Severity::ERROR, Severity::ALERT, Severity::CRITICAL => self::ERROR,
				Severity::SUCCESS => self::SUCCESS,
				Severity::STEP => self::STEP,
				Severity::WARNING => self::WARNING,
				default => null,
			});
		}
	}

	private function printColor(string $content, ?int $color = null): void
	{
		if ($color) {
			echo sprintf("\033[%dm%s\033[0m", $color, $content) . "\n";
		} else {
			echo $content . "\n";
		}
	}

	private function printException(Throwable $exception): void
	{
		echo $exception;
	}

	/**
	 * @param array<string, int> $summaryStack
	 */
	private function printSummary(array $summaryStack): void
	{
		if (!$summaryStack) {
			return;
		}

		$this->printColor('Summary of logs:');
		foreach ($summaryStack as $name => $count) {
			$this->printColor(sprintf("\t%s: %d", $name, $count));
		}
	}

}
