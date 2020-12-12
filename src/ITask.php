<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

interface ITask
{

	/**
	 * @return void|bool
	 */
	public function run();

}
