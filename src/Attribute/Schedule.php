<?php declare(strict_types = 1);

namespace WebChemistry\TaskRunner\Attribute;

final class Schedule implements ISchedule
{

	/**
	 * * = any value
	 * , = value list separator (5-10, at 5 and 10 minute)
	 * - = range of values (5-10, at 5 and 6 and 7 and 8 and 9 and 10 minute)
	 * / = step values (non standard)
	 *
	 * @param string $minute (0-59)
	 * @param string $hour (0-23)
	 * @param string $day day of month (1 - 31)
	 * @param string $month (1 - 12)
	 * @param string $dayWeek Sunday to Saturday (0 - 6) OR sun, mon, tue, wed, thu, fri, sat
	 */
	public function __construct(
		public readonly string|int $minute = '*',
		public readonly string|int $hour = '*',
		public readonly string|int $day = '*',
		public readonly string|int $month = '*',
		public readonly string|int $dayWeek = '*',
	)
	{
	}

	public function getNormalized(): string
	{
		return implode(' ', array_map(
			fn (string $value) => preg_replace('#\s+#', '', $value),
			$this->toArray(),
		));
	}

	public function isEqual(Schedule $schedule): bool
	{
		return $this->getNormalized() === $schedule->getNormalized();
	}

	/**
	 * @return string[]
	 */
	public function toArray(): array
	{
		return [
			(string) $this->minute,
			(string) $this->hour,
			(string) $this->day,
			(string) $this->month,
			(string) $this->dayWeek,
		];
	}

	public function __toString(): string
	{
		return implode(' ', $this->toArray());
	}

}
