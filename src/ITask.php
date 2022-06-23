<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use Psr\Log\LoggerInterface;

interface ITask
{

	/**
	 * @return void|bool
	 */
	public function run(LoggerInterface $logger);

}
