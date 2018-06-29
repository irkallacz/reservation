<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.01.2018
 * Time: 14:52
 */

namespace App\Model\Orm;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\Repository;
use Tracy\Debugger;

/**
 * @method Person getById($id)
 * @method ICollection|Person[] findByFilter(array $filter)
 */
final class PersonsRepository extends Repository
{
	/**
	 * @return array
	 */
	static function getEntityClassNames(): array
	{
		return [Person::class];
	}

	/**
	 * @param string $rc
	 * @return Person|mixed|\Nextras\Orm\Entity\IEntity|null
	 */
	public function getByRc($rc)
	{
		return $this->getBy(['rc' => $rc]);
	}

	/**
	 * @param string $mail
	 * @return Person|mixed|\Nextras\Orm\Entity\IEntity|null
	 */
	public function getByMail($mail)
	{
		return $this->getBy(['mail' => $mail]);
	}

}