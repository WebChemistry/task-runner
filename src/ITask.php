<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use WebChemistry\TaskRunner\Logger\TaskLogger;

interface ITask
{

	/**
	 * @return void|bool
	 */
	public function run(TaskLogger $logger);

}
