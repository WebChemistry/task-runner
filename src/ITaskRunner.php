<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

interface ITaskRunner
{

	public function run(string $instanceOf): void;

}
