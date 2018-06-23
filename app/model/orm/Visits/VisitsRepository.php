<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.01.2018
 * Time: 14:52
 */

namespace App\Model\Orm;


use Nette\Utils\DateTime;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\Repository;
/**
 * @method Visit getById($id)
 */
final class VisitsRepository extends Repository
{
	/**
	 * @return array
	 */
	static function getEntityClassNames()
	{
		return [Visit::class];
	}

	/**
	 * @param mixed $group
	 * @return ICollection
	 */
	public function findFutureByGroup($group)
	{
		return $this->findBy([
			'group' => $group,
			'dateStart>' => new DateTime()
		])->orderBy('dateStart');
	}
}