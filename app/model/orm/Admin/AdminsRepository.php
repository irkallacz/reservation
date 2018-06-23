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
 * @method Admin getById($id)
 * @method ICollection|Admin[] findByFilter(array $filter)
 */
final class AdminsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Admin::class];
	}

	/**
	 * @param string $mail
	 * @return Admin|mixed|\Nextras\Orm\Entity\IEntity|null
	 */
	public function getByMail($mail)
	{
		return $this->getBy(['mail' => $mail]);
	}

}