<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebChemistry\TaskRunner\DI\TaskRunnerExtension;
use WebChemistry\TaskRunner\Extension\TaskRunnerExceptionPrinterExtension;
use WebChemistry\TaskRunner\Extension\TaskRunnerProgressExtension;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Logger\StdoutLogger;
use WebChemistry\TaskRunner\TaskRunner;

final class RunCommand extends Command
{

	protected static $defaultName = 'tasks:run';

	private ITaskRunner $taskRunner;

	/**
	 * @param TaskRunnerExtension[] $extensions
	 */
	public function __construct(
		ITaskRunner $taskRunner,
		array $extensions = [],
	)
	{
		parent::__construct();

		$taskRunner = new TaskRunner($taskRunner->getTasks(), new StdoutLogger());
		$taskRunner = $taskRunner->withExtension(new TaskRunnerExceptionPrinterExtension());

		foreach ($extensions as $extension) {
			$taskRunner = $taskRunner->withExtension($extension);
		}

		$taskRunner = $taskRunner->withLogger(new StdoutLogger());

		$this->taskRunner = $taskRunner;
	}

	protected function configure(): void
	{
		$this->setDescription('Run task by class name or task name or schedule.')
			->addArgument('id', InputArgument::REQUIRED, 'Schedule or class name or task name')
			->addOption('progress', 'p', InputOption::VALUE_NONE, 'Shows task progress.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var string|null $id */
		$id = $input->getArgument('id');

		$taskRunner = $this->taskRunner;

		if ($input->getOption('progress') === true) {
			$taskRunner = $taskRunner->withExtension(new TaskRunnerProgressExtension());
		}

		$result = $taskRunner->run($id);

		return $result->isSuccess() ? self::SUCCESS : self::FAILURE;
	}

}
