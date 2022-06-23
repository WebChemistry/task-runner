<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Logger;

use Psr\Log\LoggerInterface;
use Stringable;

final class StackLogger implements LoggerInterface
{

	/** @var array{mixed, string, mixed[]}[] */
	private array $stack = [];

	public function emergency(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::EMERGENCY, $message, $context);
	}

	public function alert(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::ALERT, $message, $context);
	}

	public function critical(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::CRITICAL, $message, $context);
	}

	public function error(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::ERROR, $message, $context);
	}

	public function warning(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::WARNING, $message, $context);
	}

	public function notice(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::NOTICE, $message, $context);
	}

	public function info(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::INFO, $message, $context);
	}

	public function debug(Stringable|string $message, array $context = []): void
	{
		$this->log(Severity::DEBUG, $message, $context);
	}

	/**
	 * @param mixed $level
	 * @param mixed[] $context
	 */
	public function log($level, Stringable|string $message, array $context = []): void
	{
		$this->stack[] = [$level, (string) $message, $context];
	}

	/**
	 * @return array{mixed, string, mixed[]}[]
	 */
	public function getStack(): array
	{
		return $this->stack;
	}

}
