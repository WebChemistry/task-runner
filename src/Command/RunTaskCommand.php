<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Utilitte\Asserts\TypeAssert;
use WebChemistry\TaskRunner\DI\TaskRunnerExtension;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\ITaskRunner;
use WebChemistry\TaskRunner\Result\TaskRunnerResult;

final class RunTaskCommand extends Command
{

	protected static $defaultName = 'task:run';

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

		foreach ($extensions as $extension) {
			$taskRunner = $taskRunner->withExtension($extension);
		}
		
		$this->taskRunner = $taskRunner;
	}

	protected function configure(): void
	{
		$this->setDescription('Run task by class name or name or group.')
			->addArgument('className', InputArgument::OPTIONAL, 'Run task by class name.')
			->addOption('group', null, InputOption::VALUE_REQUIRED, 'Run task by group.')
			->addOption('name', null, InputOption::VALUE_REQUIRED, 'Run task by name.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var string|null $className */
		$className = $input->getArgument('className');
		/** @var string|null $group */
		$group = $input->getOption('group');
		/** @var string|null $name */
		$name = $input->getOption('name');
		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$results = [];

		if (!$className && !$group && !$name) {
			$names = array_map('get_class', $this->taskRunner->getTasks());

			$question = new ChoiceQuestion('Select task to run, please:', $names);

			$result = $helper->ask($input, $output, $question);
			$key = array_search($result, $names, true);

			$results[] = $this->taskRunner->run(TypeAssert::classStringOf($names[$key], ITask::class));
		} else {

			if ($className) {
				$results[] = $this->taskRunner->run(TypeAssert::classStringOf(strtr($className, ['/' => '\\']), ITask::class));
			}

			if ($name) {
				$results[] = $this->taskRunner->runByName($name);
			}

			if ($group) {
				$results[] = $this->taskRunner->runByGroup($group);
			}

		}

		$result = new TaskRunnerResult(array_merge(...array_map(fn (TaskRunnerResult $result) => $result->results, $results)));

		return $result->isSuccess() ? self::SUCCESS : self::FAILURE;
	}

}
