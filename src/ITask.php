<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use Psr\Log\LoggerInterface;
use WebChemistry\TaskRunner\Attribute\Schedule;

interface ITask
{

	/**
	 * @return void|bool
	 */
	public function run(LoggerInterface $logger);

	public function getId(): ?string;

	public function getDescription(): ?string;

}
