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
use App\Forms\PatientFormFactory;
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
		$form = PatientFormFactory::create();

		$form->addSubmit('ok', 'OK');

		$form->onValidate[] = function (Form $form, ArrayHash $values)
		{
			$person = $this->orm->persons->getByMail($values->mail);
			if (($person)and($person !== $this->person)) $form->addError('V databázi se již nachází osoba s Vaším emailem!');

			if ($values->rc){
				$person = $this->orm->persons->getByRc($values->rc);
				if (($person)and($person !== $this->person)) $form->addError('V databázi se již nachází osoba s Vaším rodným číslem!');
			}
		};

		$form->onSuccess[] = function (Form $form, ArrayHash $values)
		{
			$person = $this->person;
			$person->name = $values->name;
			$person->surname = $values->surname;
			$person->mail = $values->mail;
			$person->phone = $values->phone;
			$person->address = $values->address;
			$person->rc = $values->rc;

			$this->orm->persistAndFlush($person);

			$this->flashMessage('Profil byl uložen');
			$this->redirect('default');
		};

		return $form;
	}

	/**
	 * @return Form
	 */
	protected function createComponentPasswordForm(): Form
	{
		$form = PasswordFormFactory::create();

		$form->onSuccess[] = function (Form $form, ArrayHash $values){
			$this->person->password = Passwords::hash($values->password);
			$this->orm->persons->persistAndFlush($this->person);

			$this->flashMessage('Heslo bylo změněno');
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

		$formFactory = new GroupLogInFormFactory($groups);

		$form = $formFactory->create();

		$form->onValidate[] = function (Form $form, ArrayHash $values){
			$group = $this->orm->groups->getById($values->group);

			if (!Passwords::verify($values->password, $group->password)){
				$form->addError('Nesprávné heslo skupiny');
			}
		};

		$form->onSuccess[] = function (Form $form, ArrayHash $values){
			$this->person->groups->add($values->group);
			$this->orm->persistAndFlush($this->person);

			$this->flashMessage('Byl jste zapsán do skupiny');
			$this->redirect('default');
		};

		return $form;
	}

	/**
	 * @param int $id
	 * @throws AbortException
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