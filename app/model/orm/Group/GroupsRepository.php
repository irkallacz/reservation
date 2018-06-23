<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.01.2018
 * Time: 14:52
 */

namespace App\Model\Orm;


use Nextras\Orm\Repository\Repository;
/**
 * @method Group getById($id)
 */
final class GroupsRepository extends Repository
{
	static function getEntityClassNames()
	{
		return [Group::class];
	}
}