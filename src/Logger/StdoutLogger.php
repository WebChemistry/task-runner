<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

final class StdoutLogger extends AbstractLogger
{

	private const STEP = 36;
	private const SUCCESS = 32;
	private const WARNING = 33;
	private const ERROR = 31;

	public function __construct(
		private readonly bool $colors = true,
	)
	{
	}

	public function log($level, Stringable|string $message, array $context = []): void
	{
		if (!is_string($level)) {
			$this->printColor($message);
		} else {
			$this->printColor((string) $message, match (strtolower($level)) {
				LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::ERROR => self::ERROR,
				LogLevel::WARNING => self::WARNING,
				'step' => self::STEP,
				'success' => self::SUCCESS,
				default => null,
			});
		}
	}

	private function printColor(string $content, ?int $color = null): void
	{
		if ($this->colors && $color) {
			echo sprintf("\033[%dm%s\033[0m", $color, $content) . "\n";
		} else {
			echo $content . "\n";
		}
	}

}
