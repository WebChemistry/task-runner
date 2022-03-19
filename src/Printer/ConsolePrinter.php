<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Printer;

final class ConsolePrinter implements IPrinter
{

	public function println(string $content): void
	{
		echo sprintf("%s\n", $content);
	}

	public function printStep(string $content): void
	{
		$this->println(sprintf('\u001b[36m%s\u001b[0m', $content));
	}

	public function printError(string $content): void
	{
		$this->println(sprintf('\u001b[31m%s\u001b[0m', $content));
	}

}
