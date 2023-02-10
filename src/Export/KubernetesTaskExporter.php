<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Export;

use Symfony\Component\Yaml\Yaml;
use WebChemistry\TaskRunner\Export\Group\TaskExportGrouper;
use WebChemistry\TaskRunner\ITask;
use WebChemistry\TaskRunner\ITaskRunner;

final class KubernetesTaskExporter implements ITaskExporter
{

	/**
	 * @param string[] $command
	 * @param mixed[] $containerSpec
	 */
	public function __construct(
		private ITaskRunner $taskRunner,
		private string $image,
		private array $command,
		private array $containerSpec = [],
		private string $restartPolicy = 'Never',
		private int $backoffLimit = 0,
		private ?string $prefix = null,
	)
	{
	}

	public function export(): string
	{
		$grouper = new TaskExportGrouper($this->taskRunner);
		$documents = [];

		foreach ($grouper->getGroups() as $group) {
			$comment = "# Composition:\n# \t" . implode("\n# \t", array_map(
					fn (ITask $task) => implode(' - ', array_filter([$task::class, $task->getDescription()])),
					$group->getTasks(),
				)) . "\n";
			$schedule = $group->getSchedule()->getNormalized();

			$documents[] = $comment . Yaml::dump(
					$this->createSpecification($schedule, $group->getId(), [
						'restartPolicy' => $this->restartPolicy,
						'containers' => [
							array_merge(
								[
									'name' => 'php',
									'image' => $this->image,
									'command' => array_map(
										fn (string $string) => strtr($string, [
											'%c%' => $group->getId(),
										]),
										$this->command,
									),
								],
								$this->containerSpec,
							),
						],
					]),
					PHP_INT_MAX,
					flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK,
				);
		}

		return implode("---\n", $documents);
	}

	private function createSpecification(string $schedule, string $name, array $spec): array
	{
		return [
			'apiVersion' => 'batch/v1',
			'kind' => 'CronJob',
			'metadata' => [
				'name' => $this->prefix . $name,
			],
			'spec' => [
				'schedule' => $schedule,
				'jobTemplate' => [
					'spec' => [
						'backoffLimit' => $this->backoffLimit,
						'template' => [
							'spec' => $spec,
						],
					],
				],
			],
		];
	}

}
