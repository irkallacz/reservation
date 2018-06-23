<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.1.2018
 * Time: 21:38
 */

namespace App\Model\Orm;

use Nextras\Orm\Entity\Entity;
use DateTimeImmutable;
use Nextras\Orm\Relationships\ManyHasMany;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Person
 *
 * @property int                    $id				{primary}

 * @property string      			$name
 * @property string      			$surname
 * @property string|null      		$rc

 * @property string      			$mail
 * @property string|null     		$phone
 * @property string|null      		$address

 * @property string      			$password
 * @property string|null      		$note

 * @property ManyHasMany|Group[]  	$groups 		{m:m Group::$persons}

 * @property DateTimeImmutable      $dateUpdate

 * @property VisitRequest|null  	$visitRequest	{1:1 VisitRequest::$person}
 * @property OneHasMany|Visit[]		$visits			{1:m Visit::$person, orderBy=[dateStart=ASC]}

 * @property-read string      		$fullName 		{virtual}
 */
final class Person extends Entity
{
	/**
	 * @return string
	 */
	protected function getterFullName()
	{
		return $this->surname . ' ' . $this->name;
	}

	/**
	 * @return Visit|\Nextras\Orm\Entity\IEntity|null
	 */
	public function getNextVisit()
	{
		return $this->visits->get()->getBy([
			'dateStart>' => new DateTimeImmutable(),
		]);
	}

}
