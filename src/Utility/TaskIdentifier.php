<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Utility;

final class TaskIdentifier
{

	/**
	 * @param class-string $className
	 */
	public static function extractId(string $className): string
	{
		$pos = strrpos($className, '\\');

		if ($pos !== false) {
			$className = substr($className, $pos + 1);
		}

		return lcfirst($className);
	}

}
