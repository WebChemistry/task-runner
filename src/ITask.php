<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

use WebChemistry\TaskRunner\Printer\IPrinter;

interface ITask
{

	/**
	 * @return void|bool
	 */
	public function run(IPrinter $printer);

}
