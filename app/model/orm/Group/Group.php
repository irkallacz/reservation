<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.1.2018
 * Time: 21:38
 */

namespace App\Model\Orm;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\Entity;
use DateTimeImmutable;
use Nextras\Orm\Relationships\ManyHasMany;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Group
 *
 * @property int                    		$id				{primary}

 * @property string      					$title
 * @property string      					$password
 * @property boolean      					$active

 * @property ManyHasMany|Person[]  			$persons  		{m:m Person::$groups, isMain=true, orderBy=[surname=ASC, name=ASC]}
 *
 * @property DateTimeImmutable      $dateUpdate		{default now}

 * @property OneHasMany|Visit[]				$visits				{1:m Visit::$group}
 * @property-read string      				$roleName 			{virtual}
 * @property-read int      					$activeVisitsCount 	{virtual}
 * @property-read DateTimeImmutable|null 	$visitsLimit 		{virtual}
 */
final class Group extends Entity
{
	/**
	 * @return int
	 */
	protected function getterActiveVisitsCount(): int
	{
		return $this->visits->get()->findBy([
			'open' => TRUE,
			'dateStart>' => new \DateTime()
		])->count();
	}

	/**
	 * @return DateTimeImmutable|NULL
	 */
	protected function getterVisitsLimit()
	{
		/** @var Visit $lastVisit*/
		$lastVisit = $this->visits->get()
			->findBy(['dateStart>' => new \DateTime])
			->orderBy('dateLimit', ICollection::DESC)
			->fetch();

		return ($lastVisit) ? $lastVisit->dateLimit : NULL;
	}

}
