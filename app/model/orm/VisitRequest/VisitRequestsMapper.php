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

final class VisitRequestsMapper extends Mapper
{
	/**
	 * @param array $filter
	 * @return QueryBuilder
	 */
	public function findByFilter($filter): QueryBuilder
	{
		$query = $this->builder();

		foreach($filter as $condition => $value)
			if ($condition === 'active') {
				$value = boolval($value);
				$query->andWhere('%column = %b', $condition, $value);
			} else {
				$query->andWhere('%column LIKE %like_', $condition, $value);
			}

		return $query;
	}

}