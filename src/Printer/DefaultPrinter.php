<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Printer;

use Throwable;
use WebChemistry\TaskRunner\Logger\TaskLogger;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;

final class DefaultPrinter
{

	private const STEP = 36;
	private const SUCCESS = 32;
	private const WARNING = 33;
	private const ERROR = 31;

	public function __construct(
		private TaskRunnerResult $result,
	)
	{
	}

	public function print(bool $exit = false): void
	{
		$succesed = $errored = [];

		foreach ($this->result->run as $run) {
			if ($run->success) {
				$succesed[] = $run;
			} else {
				$errored[] = $run;
			}
		}

		foreach ($succesed as $result) {
			$stack = $result->logger->getStack();

			if ($stack) {
				$this->printColor(sprintf('Task %s successed and had following log:', $result->task::class), self::SUCCESS);

				$this->printStack($stack);
			} else {
				$this->printColor(sprintf('Task %s successed.', $result->task::class), self::SUCCESS);
			}
		}

		foreach ($errored as $result) {
			$stack = $result->logger->getStack();

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

		if ($exit && $errored) {
			exit($this->result->hasException() ? 255 : 1);
		}
	}

	/**
	 * @param array{string, int}[] $stack
	 */
	private function printStack(array $stack): void
	{
		static $colors = [
			TaskLogger::STEP => self::STEP,
			TaskLogger::ERROR => self::ERROR,
			TaskLogger::SUCCESS => self::SUCCESS,
			TaskLogger::WARNING => self::WARNING,
		];

		foreach ($stack as [$content, $type]) {
			$this->printColor($content, $colors[$type] ?? null);
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
		echo (string) $exception;
	}

}
