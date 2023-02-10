<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\DI\MissingServiceException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Tracy\Bar;
use WebChemistry\TaskRunner\Export\ITaskExporter;
use WebChemistry\TaskRunner\Export\KubernetesTaskExporter;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\TaskRunner;
use WebChemistry\TaskRunner\Tracy\TaskRunnerBar;

final class TaskRunnerExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'exporter' => Expect::structure([
				'kubernetes' => Expect::structure([
					'prefix' => Expect::string()->nullable(),
					'image' => Expect::string()->required(),
					'command' => Expect::array(Expect::string())->required(),
					'containerSpec' => Expect::array(),
 				]),
			]),
			'tracy' => Expect::structure([
				'command' => Expect::string('bin/console task:run %s%'),
			])
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		$this->compiler->addExportedType(ITaskRunner::class);

		$builder->addDefinition($this->prefix('taskRunner'))
			->setType(ITaskRunner::class)
			->setFactory(TaskRunner::class);

		$builder->addDefinition($this->prefix('bar'))
			->setFactory(TaskRunnerBar::class, [$config->tracy->command]);

		if ($config->exporter->kubernetes) {
			$exporter = $config->exporter->kubernetes;

			$builder->addDefinition($this->prefix('exporter'))
				->setType(ITaskExporter::class)
				->setFactory(KubernetesTaskExporter::class, [
					'image' => $exporter->image,
					'command' => $exporter->command,
					'containerSpec' => $exporter->containerSpec,
					'prefix' => $exporter->prefix,
				]);
		}
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
