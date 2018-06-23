<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.01.2018
 * Time: 14:42
 */

namespace App\Model\Orm;

use Nextras\Dbal\QueryBuilder\QueryBuilder;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\Mapper;

final class PersonsMapper extends Mapper
{

	/**
	 * @param array $filter
	 * @return QueryBuilder|ICollection
	 */
	public function findByFilter($filter)
	{
		$query = $this->builder();

		foreach($filter as $condition => $value)
			$query->andWhere('%column LIKE %like_', $condition, $value);

		return $query;
	}

}