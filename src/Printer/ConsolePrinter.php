<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Printer;

final class ConsolePrinter implements IPrinter
{

	public function __construct(
		private int $severity = self::ALL,
		private bool $colorize = true,
	)
	{
	}

	public function println(string $content): void
	{
		echo sprintf("%s\n", $content);
	}

	public function printStep(string $content): void
	{
		if (!($this->severity & self::STEP)) {
			return;
		}

		if ($this->colorize) {
			$this->println(sprintf("\033[36m%s\033[0m", $content));
		} else {
			$this->println($content);
		}
	}

	public function printError(string $content): void
	{
		if (!($this->severity & self::ERROR)) {
			return;
		}

		if ($this->colorize) {
			$this->println(sprintf("\033[31m%s\033[0m", $content));
		} else {
			$this->println($content);
		}
	}

	public function printWarning(string $content): void
	{
		if (!($this->severity & self::WARNING)) {
			return;
		}

		if ($this->colorize) {
			$this->println(sprintf("\033[33m%s\033[0m", $content));
		} else {
			$this->println($content);
		}
	}

	public function printInfo(string $content): void
	{
		if (!($this->severity & self::INFO)) {
			return;
		}

		$this->println($content);
	}

	public function printDebug(string $content): void
	{
		if (!($this->severity & self::DEBUG)) {
			return;
		}

		$this->println($content);
	}

}
