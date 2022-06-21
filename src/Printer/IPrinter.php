<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Printer;

interface IPrinter
{

	public const DEFAULT = 0;
	public const INFO = 1;
	public const DEBUG = 2;
	public const ERROR = 4;
	public const STEP = 8;
	public const WARNING = 16;
	public const ALL = self::INFO | self::DEBUG | self::ERROR | self::STEP | self::WARNING;

	public function println(string $content): void;

	public function printStep(string $content): void;

	public function printError(string $content): void;

	public function printWarning(string $content): void;

	public function printInfo(string $content): void;

	public function printDebug(string $content): void;

}
