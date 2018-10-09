<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.01.2018
 * Time: 14:42
 */

namespace App\Model\Orm;

use DateTimeImmutable;
use Nextras\Orm\Entity\Entity;
use Tracy\Debugger;

/**
 * Visit
 *
 * @property int                    $id				{primary}

 * @property DateTimeImmutable      $dateStart
 * @property DateTimeImmutable      $dateEnd
 * @property DateTimeImmutable      $dateLimit
 * @property DateTimeImmutable      $dateUpdate		{default now}

 * @property boolean      			$open			{default true}
 * @property string|null			$note

 * @property int 					$type			{enum self::TYPE_*} {default self::TYPE_ECG}
 * @property string 				$typeTitle		{virtual}
 *
 * @property Person|null  			$person      	{m:1 Person::$visits}
 * @property Group|null  			$group      	{m:1 Group::$visits}

 * @property-read float      		$length 		{virtual}
 * @property-read float      		$position 		{virtual}
 * @property-read boolean      		$isOver 		{virtual}
 * @property-read boolean      		$canLogIn 		{virtual}
 * @property-read boolean      		$canLogOut 		{virtual}
 * @property-read boolean      		$isInProgress 	{virtual}
 * @property-read string 			$timestamp 		{virtual}
 */
final class Visit extends Entity
{
	const TYPE_ECG  	= 1;
	const TYPE_SPIRO  	= 2;

	protected  function getterTypeTitle(): string
	{
		return ($this->type == self::TYPE_SPIRO) ? 'Spiroergometrie' : 'Sportovní prohlídka';
	}

	/**
	 * @return float
	 */
	protected function getterLength(): float
	{
		$interval = $this->dateEnd->diff($this->dateStart);
		return  self::getMinutes($interval) / 6;
	}

	/**
	 * @return float
	 */
	protected function getterPosition(): float
	{
		$dayStart = $this->dateStart->setTime(6, 0);
		$interval = $this->dateStart->diff($dayStart);
		return  self::getMinutes($interval) / 6;
	}

	/**
	 * @param \DateInterval $interval
	 * @return int
	 */
	private static function getMinutes(\DateInterval $interval): int
	{
		return $interval->h * 60 + $interval->i;
	}

	/**
	 * @return bool
	 */
	protected function getterIsOver(): bool
	{
		return $this->dateStart < new DateTimeImmutable();
	}

	/**
	 * @return bool
	 */
	protected function getterCanLogIn(): bool
	{
		return ($this->open) and ($this->dateLimit > new DateTimeImmutable());
	}

	/**
	 * @return bool
	 */
	protected function getterCanLogOut(): bool
	{
		return $this->dateLimit > new DateTimeImmutable();
	}

	/**
	 * @return bool
	 */
	protected function getterIsInProgress(): bool
	{
		$now = new DateTimeImmutable();
		return ($this->dateStart > $now) and ($this->dateEnd < $now);
	}

	public function logOut()
	{
		$this->person = NULL;
		$this->open = TRUE;
		$this->note = NULL;
		$this->type = self::TYPE_ECG;
	}

	/**
	 * @param VisitRequest $request
	 * @return bool
	 */
	public function canSatisfyRequest(VisitRequest $request): bool
	{
		if (($request->dateStart > $this->dateStart)or($request->dateEnd < $this->dateEnd)) return FALSE;
		if (!in_array(($this->dateStart->format('N')-1), $request->daysArray)) return FALSE;
		if (($this->group)and(!$request->person->groups->has($this->group))) return FALSE;
		if ($this->open = FALSE) return FALSE;

		return TRUE;
	}

	public function getterTimestamp(): string
	{
		$timestamp = clone $this->dateUpdate;
		return $timestamp->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');
	}
}