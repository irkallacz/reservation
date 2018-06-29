<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.02.2018
 * Time: 15:21
 */

namespace App\AdminModule\Presenters;

use Nette\Forms\Container;
use Nette\Forms\Form;
use Nextras\Datagrid\Datagrid;
use Nextras\Orm\Collection\ICollection;

final class VolunteerPresenter extends AdminPresenter
{

	protected function createComponentVolunteerGrid(): Datagrid
	{
		$grid = new Datagrid();
		$grid->addColumn('id', '');
		$grid->addColumn('active', 'Aktivní');
		$grid->addColumn('dateStart', 'Od')->enableSort();
		$grid->addColumn('dateEnd', 'Do')->enableSort();
		$grid->addColumn('days', 'Ve dnech');
		$grid->addColumn('dateAdd', 'Vloženo')->enableSort();
		$grid->addColumn('person', 'Osoba');

		$grid->setDataSourceCallback([$this, 'getRequestCollection']);

		$grid->setPagination(50, function($filter, $order){
			return $this->getRequestCollection($filter, NULL)->count();
		});

		$grid->setFilterFormFactory(function() {
			$form = new Container();
			$form->addCheckbox('active')->setDefaultValue(TRUE);

			return $form;
		});

		$grid->addCellsTemplate(__DIR__ . '/templates/volunteer-grid.latte');


		return $grid;
	}

	/**
	 * @param array $filter
	 * @param array $order
	 * @return ICollection
	 */
	public function getRequestCollection(array $filter, array $order): ICollection
	{
		if ($order) {
			$sort = $order[0];
			$direction = ($order[1] == 'DESC') ? ICollection::DESC : ICollection::ASC;
		}else {
			$sort = 'id';
			$direction = ICollection::ASC;
		}

		$requests = ($filter) ? $this->orm->visitRequests->findByFilter($filter)->orderBy($sort, $direction) : $this->orm->visitRequests->findAll()->orderBy($sort, $direction);

		return $requests;
	}

}