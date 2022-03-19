<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Printer;

interface IPrinter
{

	public function println(string $content): void;

	public function printStep(string $content): void;

	public function printError(string $content): void;

}
