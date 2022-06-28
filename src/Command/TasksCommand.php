<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Utility\TaskRunnerUtility;

final class TasksCommand extends Command
{

	protected static $defaultName = 'tasks';

	public function __construct(
		private ITaskRunner $taskRunner,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setDescription('Prints tasks.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$groups = TaskRunnerUtility::getTaskByGroups($others = $this->taskRunner->getTasks());

		foreach ($groups as $group => $tasks) {
			$output->writeln(sprintf('<comment>%s</comment>', $group));

			foreach ($tasks as $task) {
				$this->printTask($output, $task);

				if (($key = array_search($task, $others, true)) !== false) {
					unset($others[$key]);
				}
			}
		}

		if (!$others) {
			return self::SUCCESS;
		}

		$output->writeln('<info>others</info>');

		foreach ($others as $task) {
			$this->printTask($output, $task);
		}

		return self::SUCCESS;
	}

	private function printTask(OutputInterface $output, ITask $task): void
	{
		if ($taskName = TaskRunnerUtility::getTaskName($task)) {
			$output->writeln(sprintf("\t%s(name=%s)", $task::class, $taskName));
		} else {
			$output->writeln(sprintf("\t%s", $task::class));
		}
	}

}
