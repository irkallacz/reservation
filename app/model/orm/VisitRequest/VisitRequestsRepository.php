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
/**
 * @method VisitRequest getById($id)
 * @method ICollection|VisitRequest[] findByFilter(array $filter)
 */
final class VisitRequestsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [VisitRequest::class];
	}
}