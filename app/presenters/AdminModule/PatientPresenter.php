<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.02.2018
 * Time: 13:45
 */

namespace App\AdminModule\Presenters;


use Nette\Forms\Container;
use Nette\Utils\Paginator;
use Nextras\Datagrid\Datagrid;
use Nextras\Orm\Collection\ICollection;
use Tracy\Debugger;

final class PatientPresenter extends AdminPresenter
{

	/**
	 * @return Datagrid
	 */
	protected function createComponentPatientGrid(): Datagrid
	{
		$grid = new Datagrid();
		$grid->addColumn('id', '');
		$grid->addColumn('surname', 'Příjmení')->enableSort(Datagrid::ORDER_ASC);
		$grid->addColumn('name', 'Jméno')->enableSort();
		$grid->addColumn('rc', 'Rodné číslo')->enableSort();
		$grid->addColumn('mail', 'E-mail')->enableSort();
		$grid->addColumn('phone', 'Telefon')->enableSort();

		$grid->setDataSourceCallback([$this, 'getPatientsCollection']);

		$grid->setPagination(50, function($filter){
			return $this->getPatientsCollection($filter)->count();
		});

		$grid->setFilterFormFactory(function() {
			$form = new Container();
			$form->addText('name');
			$form->addText('surname');
			$form->addText('rc');
			$form->addText('mail', NULL, 60);
			$form->addText('phone');

			return $form;
		});

		$grid->addCellsTemplate(__DIR__ . '/templates/patient-grid.latte');

		return $grid;
	}

	/**
	 * @param array $filter
	 * @param array|null $order
	 * @param Paginator|null $paginator
	 * @return ICollection
	 */
	public function getPatientsCollection(array $filter, array $order = null, Paginator $paginator = null): ICollection
	{
		$patients = ($filter) ? $this->orm->persons->findByFilter($filter) : $this->orm->persons->findAll();

		if ($order) {
			$column = $order[0];
			$direction = ($order[1] == 'DESC') ? ICollection::DESC : ICollection::ASC;
			$patients = $patients->orderBy($column, $direction);
		}

		if ($paginator) {
			$patients = $patients->limitBy($paginator->getItemsPerPage(), $paginator->getOffset());
		}

		return $patients;
	}

	public function renderView($id)
	{
		$patient = $this->orm->persons->getById($id);
		$this->template->person = $patient;
		$this->template->request = $patient->visitRequest;
	}

}