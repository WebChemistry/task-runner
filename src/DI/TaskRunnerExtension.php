<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\TaskRunner;

final class TaskRunnerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		
		$this->compiler->addExportedType(ITaskRunner::class);

		$builder->addDefinition($this->prefix('taskRunner'))
			->setType(ITaskRunner::class)
			->setFactory(TaskRunner::class);
	}

}
