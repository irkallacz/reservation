<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.02.2018
 * Time: 13:45
 */

namespace App\AdminModule\Presenters;


use Nette\Forms\Container;
use Nextras\Datagrid\Datagrid;
use Nextras\Orm\Collection\ICollection;
use Tracy\Debugger;

final class PatientPresenter extends AdminPresenter
{

	protected function createComponentPatientGrid()
	{
		$grid = new Datagrid();
		$grid->addColumn('id', '');
		$grid->addColumn('surname', 'Příjmení')->enableSort();
		$grid->addColumn('name', 'Jméno')->enableSort();
		$grid->addColumn('rc', 'Rodné číslo')->enableSort();
		$grid->addColumn('mail', 'E-mail')->enableSort();
		$grid->addColumn('phone', 'Telefon')->enableSort();

		$grid->setDataSourceCallback([$this, 'getPatientsCollection']);

		$grid->setPagination(50, function($filter, $order){
			return $this->getPatientsCollection($filter, NULL)->count();
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
	 * @param array $order
	 * @return ICollection
	 */
	public function getPatientsCollection($filter, $order)
	{
		if ($order) {
			$sort = $order[0];
			$direction = ($order[1] == 'DESC') ? ICollection::DESC : ICollection::ASC;
		}else {
			 $sort = 'surname';
			 $direction = ICollection::ASC;
		}

		$patients = ($filter) ? $this->orm->persons->findByFilter($filter)->orderBy($sort, $direction) : $this->orm->persons->findAll()->orderBy($sort, $direction);

		return $patients;
	}

	public function renderView($id)
	{
		$patient = $this->orm->persons->getById($id);
		$this->template->person = $patient;
		$this->template->request = $patient->visitRequest;
	}

}