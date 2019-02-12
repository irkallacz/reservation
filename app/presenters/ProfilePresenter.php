<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 4.2.2018
 * Time: 11:54
 */

namespace App\Presenters;

use App\Forms\GroupLogInFormFactory;
use App\Forms\PasswordFormFactory;
use App\Forms\UserFormFactory;
use App\Forms\VisitRequestFormFactory;
use App\Model\Orm\VisitRequest;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

final class ProfilePresenter extends UserPresenter
{

	/**
	 *
	 */
	public function renderDefault()
	{
		$this->template->person = $this->person;
		$this->template->request = $this->person->visitRequest;
	}

	/**
	 * @return Form
	 */
	protected function createComponentUserForm(): Form
	{
		$form = UserFormFactory::create();

		$form->addText('address', 'Adresa:', 40)
			->setNullable();

		$form->addText('rc', 'Rodné číslo:', 11)
			->setAttribute('placeholder', '______/____')
			->setOption('description', '(000000/0000)')
			->setNullable()
			->addCondition(Form::FILLED)
				->addRule(Form::PATTERN, 'Jste si jistí rodným číslem?', '[0-9]{2}[0156][0-9][0-3][0-9]/[0-9]{3,4}');

		$form->addSubmit('ok', 'OK');

		$form->onValidate[] = function (Form $form)
		{
			$values = $form->getValues();

			$person = $this->orm->persons->getByMail($values->mail);
			if (($person)and($person !== $this->person)) $form->addError('V databázi se již nachází osoba s Vaším emailem!');
		};

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();

			$person = $this->person;
			$person->name = $values->name;
			$person->surname = $values->surname;
			$person->mail = $values->mail;
			$person->phone = $values->phone;
			$person->address = $values->address;
			$person->rc = $values->rc;

			$this->orm->persistAndFlush($person);

			$this->flashMessage('Profil uložen');
			$this->redirect('default');
		};

		return $form;
	}

	/**
	 *
	 */
	public function actionEdit()
	{
		$this['userForm']->setDefaults($this->person->toArray());
	}


	/**
	 * @return Form
	 */
	protected function createComponentGroupForm(): Form
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

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function actionGroupLogOut(int $id)
	{
		$group = $this->orm->groups->getById($id);
		$group->persons->remove($this->person);
		$this->orm->persistAndFlush($group);

		$this->flashMessage('Byl jste odebrán ze skupiny');
		$this->redirect('default');
	}


	/**
	 * @return Form
	 */
	protected function createComponentVisitRequestForm(): Form
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

	/**
	 *
	 */
	public function actionPersonalForm() {
		$fields = [
			'fullName' => $this->person->fullName,
			'rc' => $this->person->rc,
			'address' => $this->person->address,
			'mail' => $this->person->mail,
		];

		if ($this->person->getNextVisit()) {
			$fields['dateVisit'] = $this->person->getNextVisit()->dateStart->format('d. m. Y');
		}

		$pdf = new \FPDM(__DIR__.'/templates/form.pdf');
		$pdf->Load($fields, true);
		$pdf->Merge();


		$pdf->Output('D', $this->person->fullName.'.pdf');
	}


	/**
	 *
	 */
	public function actionVisitRequest()
	{
		if  ($this->person->visitRequest)
			$this['visitRequestForm']->setDefaults($this->person->visitRequest->toArray());
	}
}