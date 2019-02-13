<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.02.2018
 * Time: 13:45
 */

namespace App\AdminModule\Presenters;


use App\Forms\GroupLogInFormFactory;
use App\Forms\PatientFormFactory;
use App\Forms\UserFormFactory;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\Paginator;
use Nextras\Datagrid\Datagrid;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Tracy\Debugger;

final class PatientPresenter extends AdminPresenter
{

	/**
	 * @return Datagrid
	 */
	protected function createComponentPatientGrid(): Datagrid
	{
		$grid = new Datagrid();
		$grid->addColumn('id', ' ');
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

	/**
	 * @param int $id
	 * @param bool $edit
	 */
	public function renderView(int $id, bool $edit = false)
	{
		$patient = $this->orm->persons->getById($id);
		$this->template->person = $patient;
		$this->template->request = $patient->visitRequest;
		$this->template->edit = $edit;

		if ($edit){
			$this['patientForm']->setDefaults($patient->toArray(IEntity::TO_ARRAY_RELATIONSHIP_AS_ID));
		}
	}

	public function actionDelete(int $id){
		$person = $this->orm->persons->getById($id);
		$this->orm->remove($person);
		$this->orm->flush();

		$this->flashMessage('Pacient byl smazán');
		$this->redirect('default');
	}

	/**
	 * @return Form
	 */
	protected function createComponentPatientForm(): Form
	{
		$form = PatientFormFactory::create();

		/** @var TextInput $phoneField */
		$phoneField = $form['phone'];
		$phoneField->setRequired(FALSE);
		$phoneField->setNullable();

		$form->addTextArea('note', 'Poznámka:')
			->setNullable();

		$form->addSubmit('ok', 'OK');

		$form->onSuccess[] = function (Form $form, ArrayHash $values){
			$person = $this->orm->persons->getById($this->getParameter('id'));

			$person->surname = $values->surname;
			$person->name = $values->name;
			$person->phone = $values->phone;
			$person->mail = $values->mail;
			$person->rc = $values->rc;
			$person->address = $values->address;
			$person->note = $values->note;

			$this->orm->persons->persistAndFlush($person);
			$this->flashMessage('Údaje pacienta byly uloženy');
			$this->redirect('view', $person->id);
		};

		return $form;
	}

	/**
	 * @param $personId
	 * @param $groupId
	 * @throws \Nette\Application\AbortException
	 */
	public function actionGroupLogOut($personId, $groupId)
	{
		$group = $this->orm->groups->getById($groupId);
		$patient = $this->orm->persons->getById($personId);
		$group->persons->remove($patient);

		$this->orm->persistAndFlush($group);

		$this->flashMessage('Pacient byl odebrán ze skupiny');
		$this->redirect('Patient:view', $personId);

	}

	/**
	 * @return Form
	 */
	protected function createComponentGroupForm(): Form
	{
		$id = $this->getParameter('id');
		$person = $this->orm->persons->getById($id);
		$groups = $this->orm->groups->findBy(['id!=' => $person->groups->get()->fetchPairs(NULL, 'id')])
			->orderBy('title');

		$formfactory = new GroupLogInFormFactory($groups);
		$form = $formfactory->create();

		unset($form['password']);

		$form->onSuccess[] = function (Form $form) use ($person){
			$values = $form->getValues();
			$person->groups->add($values->group);
			$this->orm->persistAndFlush($person);

			$this->flashMessage('Pacient byl zapsán do skupiny');
			$this->redirect('view', $person->id);
		};

		return $form;
	}

	/**
	 * @param string|NULL $q
	 * @throws \Nette\Application\AbortException
	 */
	public function actionSearch(string $q = NULL)
	{
		$persons = $this->orm->persons->findByFilter(['surname' => $q]);
		$data = [];

		foreach ($persons as $person){
			$data[] = [
				'id' => $person->id,
				'title' => $person->fullName . " ($person->mail)",
			];
		}

		$this->sendJson($data);

	}

}