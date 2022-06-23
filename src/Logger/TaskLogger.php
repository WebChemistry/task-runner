<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Logger;

final class TaskLogger
{

	public const DEFAULT = 1;
	public const INFO = 2;
	public const DEBUG = 4;
	public const ERROR = 8;
	public const STEP = 16;
	public const SUCCESS = 32;
	public const WARNING = 64;
	public const ALL = self::DEFAULT | self::INFO | self::DEBUG | self::ERROR | self::STEP | self::SUCCESS | self::WARNING;

	/** @var array{string, int}[] */
	private array $stack = [];

	public function log(string $content): void
	{
		$this->stack[] = [$content, self::DEFAULT];
	}

	public function logStep(string $content): void
	{
		$this->stack[] = [$content, self::STEP];
	}

	public function logError(string $content): void
	{
		$this->stack[] = [$content, self::ERROR];
	}

	public function logInfo(string $content): void
	{
		$this->stack[] = [$content, self::INFO];
	}

	public function logDebug(string $content): void
	{
		$this->stack[] = [$content, self::DEBUG];
	}

	public function logSuccess(string $content): void
	{
		$this->stack[] = [$content, self::DEBUG];
	}

	public function logWarning(string $content): void
	{
		$this->stack[] = [$content, self::WARNING];
	}

	/**
	 * @return array{string, int}[]
	 */
	public function getStack(): array
	{
		return $this->stack;
	}

}
