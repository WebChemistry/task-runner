<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebChemistry\TaskRunner\Export\ITaskExporter;

final class ExportCommand extends Command
{

	protected static $defaultName = 'tasks:export';

	public function __construct(
		private ITaskExporter $exporter,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setDescription('Exports tasks');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln($this->exporter->export());

		return self::SUCCESS;
	}

}
