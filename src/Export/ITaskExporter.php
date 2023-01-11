<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Export;

interface ITaskExporter
{

	public function export(): string;

}
