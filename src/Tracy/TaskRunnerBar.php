<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Tracy;

use Nette\DI\Container;
use Nette\Utils\Helpers;
use Nette\Utils\Strings;
use Tracy\IBarPanel;
use WebChemistry\TaskRunner\ITaskRunner;

final class TaskRunnerBar implements IBarPanel
{

	public function __construct(
		private Container $container,
	)
	{
	}

	public function getTab(): string
	{
		return Helpers::capture(function (): void {
			$count = count($this->container->getByType(ITaskRunner::class)->getTasks());

			require __DIR__ . '/templates/tab.phtml';
		});
	}

	public function getPanel(): string
	{
		return Helpers::capture(function (): void {
			$taskRunner = $this->container->getByType(ITaskRunner::class);
			
			require __DIR__ . '/templates/panel.phtml';
		});
	}

}
