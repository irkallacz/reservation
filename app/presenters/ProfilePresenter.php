<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 4.2.2018
 * Time: 11:54
 */

namespace App\Presenters;

use App\Forms\GroupLogInFormFactory;
use App\Forms\UserFormFactory;
use App\Forms\VisitRequestFormFactory;
use App\Model\Orm\VisitRequest;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Tracy\Debugger;

final class ProfilePresenter extends UserPresenter
{

	public function renderDefault()
	{
		$this->template->person = $this->person;
		$this->template->request = $this->person->visitRequest;
	}

	protected function createComponentUserForm()
	{
		$form = UserFormFactory::create();

		$form->onValidate[] = function (Form $form)
		{
			$values = $form->getValues();

			$person = $this->orm->persons->getByRc($values->rc);
			if (($person)and($person !== $this->person)) $form->addError('V databázi se již nachází osoba s Vaším rodným číslem!');

			$person = $this->orm->persons->getByMail($values->mail);
			if (($person)and($person !== $this->person)) $form->addError('V databázi se již nachází osoba s Vaším emailem!');
		};

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();

			$person = $this->person;
			$person->name = $values->name;
			$person->surname = $values->surname;
			$person->rc = $values->rc;
			$person->mail = $values->mail;
			$person->phone = $values->phone;
			$person->address = $values->address;

			$this->orm->persistAndFlush($person);

			$this->flashMessage('Profil uložen');
			$this->redirect('default');
		};

		return $form;
	}

	public function actionEdit()
	{
		$this['userForm']->setDefaults($this->person->toArray());
	}


	protected function createComponentGroupForm()
	{
		$groups = $this->orm->groups->findBy([
			'active' => TRUE,
			'id!=' => $this->person->groups->get()->fetchPairs(NULL, 'id')
		])
			->orderBy('title');

		$formfactory = new GroupLogInFormFactory($groups);

		$form = $formfactory->create();

		$form->onValidate[] = function (Form $form){
			$values = $form->getValues();
			$group = $this->orm->groups->getById($values->group);

			if (!Passwords::verify($values->password, $group->password)){
				$form->addError('Nesprávné heslo skupiny');
			}
		};

		$form->onSuccess[] = function (Form $form){
			$values = $form->getValues();
			$this->person->groups->add($values->group);
			$this->orm->persistAndFlush($this->person);

			$this->flashMessage('Byl jste zapsán do skupiny');
			$this->redirect('default');
		};

		return $form;
	}

	public function actionGroupLogOut($id)
	{
		$group = $this->orm->groups->getById($id);
		$group->persons->remove($this->person);
		$this->orm->persistAndFlush($group);

		$this->flashMessage('Byl jste odebrán ze skupiny');
		$this->redirect('default');
	}


	protected function createComponentVisitRequestForm()
	{
		$form = VisitRequestFormFactory::create();

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();

			if  ($this->person->visitRequest) {
				$visitRequest = $this->person->visitRequest;

			}else {
				$visitRequest = new VisitRequest();
				$visitRequest->person = $this->person;
			}

			$visitRequest->dateStart = $values->dateStart;
			$visitRequest->dateEnd = $values->dateEnd;
			$visitRequest->dateAdd = new \DateTimeImmutable();
			$visitRequest->daysArray = $values->daysArray;
			$visitRequest->active = $values->active;

			$this->orm->persistAndFlush($visitRequest);

			$this->flashMessage('Vaše žádost byla uložena');

			$this->redirect('default');
		};
		return $form;
	}

	public function actionVisitRequest()
	{
		if  ($this->person->visitRequest)
			$this['visitRequestForm']->setDefaults($this->person->visitRequest->toArray());
	}
}