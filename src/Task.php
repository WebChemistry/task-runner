<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner;

abstract class Task implements ITask, IScheduledTask
{

	public function getId(): ?string
	{
		return null;
	}

	public function getDescription(): ?string
	{
		return null;
	}

}
