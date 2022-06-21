<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\DI\MissingServiceException;
use Tracy\Bar;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\TaskRunner;
use WebChemistry\TaskRunner\Tracy\TaskRunnerBar;

final class TaskRunnerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$this->compiler->addExportedType(ITaskRunner::class);

		$builder->addDefinition($this->prefix('taskRunner'))
			->setType(ITaskRunner::class)
			->setFactory(TaskRunner::class);

		$builder->addDefinition($this->prefix('bar'))
			->setType(TaskRunnerBar::class);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		try {
			$def = $builder->getDefinitionByType(Bar::class);

			assert($def instanceof ServiceDefinition);

			$def->addSetup('addPanel', [$builder->getDefinition($this->prefix('bar'))]);

		} catch (MissingServiceException) {
			// no need
		}
	}

}
