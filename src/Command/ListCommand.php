<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebChemistry\TaskRunner\IScheduledTask;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Utility\TaskSimpleGrouper;

final class ListCommand extends Command
{

	protected static $defaultName = 'tasks:list';

	public function __construct(
		private ITaskRunner $taskRunner,
	)
	{
		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$table = new Table($output);
		$table->setHeaders(['Class', 'ID', 'Schedule', 'Description']);
		$first = true;

		foreach (TaskSimpleGrouper::groups($this->taskRunner->getTasks()) as $groupName => $group) {
			if (!$first) {
				$table->addRow(new TableSeparator());
			}
			
			$first = false;
			
			foreach ($group as $task) {
				$table->addRow([
					$task::class,
					$task->getId() ?? ($groupName ?: 'null'),
					$task instanceof IScheduledTask ? $task->getSchedule()->getNormalized() : '',
					$task->getDescription(),
				]);
			}
		}

		$table->render();

		return self::SUCCESS;
	}

}
