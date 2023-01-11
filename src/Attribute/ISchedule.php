<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Attribute;

interface ISchedule
{

	public function getNormalized(): string;

	public function __toString(): string;

}
