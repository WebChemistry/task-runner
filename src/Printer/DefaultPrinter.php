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

	/** @var array<string, int> */
	private array $summaryStack = [];

	public function __construct(
		private readonly TaskRunnerResult $result,
	)
	{
	}

	public function print(bool $exit = false): void
	{
		$succeeded = $errored = $this->summaryStack = [];

		foreach ($this->result->run as $run) {
			if ($run->success) {
				$succeeded[] = $run;
			} else {
				$errored[] = $run;
			}
		}

		foreach ($succeeded as $result) {
			$stack = $result->logger?->getStack();

			if ($stack) {
				$this->printColor(sprintf('Task %s successed and had following log:', $result->task::class), self::SUCCESS);

				$this->printStack($stack);

			} else {
				$this->printColor(sprintf('Task %s successed.', $result->task::class), self::SUCCESS);
			}
		}

		foreach ($errored as $result) {
			$stack = $result->logger?->getStack();

			if ($stack) {
				$this->printColor(sprintf('Task %s errored and had following log:', $result->task::class), self::ERROR);

				$this->printStack($stack);

			} else {
				$this->printColor(sprintf('Task %s errored.', $result->task::class), self::ERROR);
			}

			if ($error = $result->error) {
				$this->printException($error);
			}
		}

		$this->printSummary();

		if ($exit && $errored) {
			exit($this->result->hasException() ? 255 : 1);
		}
	}

	/**
	 * @param array{mixed, string, mixed[]}[] $stack
	 */
	private function printStack(array $stack): void
	{
		foreach ($stack as [$type, $content, $context]) {
			$this->printColor($content, match ($type) {
				Severity::EMERGENCY, Severity::ERROR, Severity::ALERT, Severity::CRITICAL => self::ERROR,
				Severity::SUCCESS => self::SUCCESS,
				Severity::STEP => self::STEP,
				Severity::WARNING => self::WARNING,
				default => null,
			});

			if (isset($context['summary'])) {
				$this->summaryStack[$context['summary']] ??= 0;
				$this->summaryStack[$context['summary']]++;
			}
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

	private function printSummary(): void
	{
		if (!$this->summaryStack) {
			return;
		}

		$this->printColor('Summary of logs:');
		foreach ($this->summaryStack as $name => $count) {
			$this->printColor(sprintf("\t%s: %d", $name, $count));
		}
	}

}
