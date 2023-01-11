<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Tracy;

use Nette\DI\Container;
use Nette\Utils\Helpers;
use Tracy\IBarPanel;
use WebChemistry\TaskRunner\IScheduledTask;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Utility\TaskIdentifier;
use WebChemistry\TaskRunner\Utility\TaskSimpleGrouper;

final class TaskRunnerBar implements IBarPanel
{

	public function __construct(
		private string $command,
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
			$command = $this->command;

			require __DIR__ . '/templates/panel.phtml';
		});
	}

	public function groups(ITaskRunner $taskRunner): array
	{
		return TaskSimpleGrouper::groups($taskRunner->getTasks());
	}

}
