<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.1.2018
 * Time: 21:38
 */

namespace App\Model\Orm;

use Nette\Utils\Arrays;
use Nextras\Orm\Entity\Entity;
use DateTimeImmutable;
use Tracy\Debugger;

/**
 * Volunteer
 *
 * @property int                    $id				{primary}
 * @property int                    $days		
 * @property array                  $daysArray		{virtual}
 * @property boolean                $active

 * @property DateTimeImmutable      $dateStart
 * @property DateTimeImmutable      $dateEnd
 * @property DateTimeImmutable      $dateAdd

 * @property Admin  				$person      	{1:1 Person::$visitRequest, isMain=true}

 */
final class VisitRequest extends Entity
{
	/**
	 * Names of day in week
	 */
	const DAY_NAMES = ['pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek'];

	/**
	 * @return array
	 */
	protected function getterDaysArray(): array
	{
		return array_keys(array_filter(str_split(decbin($this->days))));
	}

	/**
	 * @param array $array
	 */
	protected function setterDaysArray(array $array)
	{
		$days = 0;
		foreach ($array as $value) $days+= pow(2, $value);
		$this->days = $days;

		return $array;
	}

	/**
	 * @return string
	 */
	public function getDaysInString(): string
	{
		return implode(', ', array_intersect_key(self::DAY_NAMES, array_flip($this->daysArray)));
	}


}
