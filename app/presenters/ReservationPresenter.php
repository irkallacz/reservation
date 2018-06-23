<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 4.2.2018
 * Time: 11:54
 */

namespace App\Presenters;

use App\Model\Email;
use App\Model\Orm\Visit;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Tracy\Debugger;

/**
 * Class ReservationPresenter
 * @package App\Presenters
 */
final class ReservationPresenter extends UserPresenter
{

	/** @var IMailer $mailer @inject */
	public $mailer;

	public function actionDefault()
	{
		$visit = $this->person->getNextVisit();
		if ($visit)
		{
			/** @var Form $form*/
			$form = $this['visitForm'];
			$form->setDefaults($visit->toArray());
		}

	}

	/**
	 *
	 */
	public function renderDefault()
	{
		$this->template->person = $this->person;
		$visit = $this->person->getNextVisit();
		$this->template->term = $visit;

		$days = [];
		$groups = [];


		$visits = $this->orm->visits->findFutureByGroup(NULL);
		foreach ($visits as $visit) {
			$days[$visit->dateStart->format('Y-m-d')][$visit->id] = $visit;
		}

		/** @var Visit[] $visits */
		$visits = $this->orm->visits->findFutureByGroup($this->person->groups->get()->fetchPairs(NULL, 'id'));

		foreach ($visits as $visit) {
			$days[$visit->dateStart->format('Y-m-d')][$visit->id] = $visit;
			$groups[$visit->group->id] = $visit->group;
		}

		$this->template->days = $days;
		$this->template->groups = $groups;
	}

	/**
	 * @param $id
	 * @throws BadRequestException
	 * @throws ForbiddenRequestException
	 * @throws \Nette\Application\AbortException
	 */
	public function actionLogIn($id) {
		$visit = $this->orm->visits->getById($id);

		if (!$visit) throw new BadRequestException('Termín nenalezen');
		if (!$visit->canLogIn) throw new ForbiddenRequestException('Na termín se již nedá přihlásit');
		if (($visit->group) and (!$this->person->groups->has($visit->group))) throw new ForbiddenRequestException('Tento termín nepatří žádné vaší skupině');

		$lastVisit = $this->person->getNextVisit();

		if ($lastVisit) {
			$this->person->visits->remove($lastVisit);
			$lastVisit->logOut();
			$this->orm->persistAndFlush($lastVisit);
		}

		$visit->person = $this->person;
		$visit->open = FALSE;
		$this->orm->persistAndFlush($visit);

		$mail = Email::newMessage();
		$mail->addTo($this->person->mail, $this->person->fullName);
		$mail->setSubject('Rezervace prohlídky');

		$template = $this->createTemplate();
		$template->setFile(__DIR__.'/templates/Email/reservation.latte');
		$template->date = $visit->dateStart;
		$mail->setBody($template);

		$this->mailer->send($mail);


		$this->flashMessage('Rezervovali jste si termín na ' . $visit->dateStart->format('d.m.Y \v H:i'));
		$this->redirect('default');
	}

	/**
	 * @throws ForbiddenRequestException
	 * @throws \Nette\Application\AbortException
	 */
	public function actionLogOut() {
		$visit = $this->person->getNextVisit();

		if ($visit) {
			if (!$visit->canLogOut) throw new ForbiddenRequestException('Z tohoto termínu se již nemůžete odhlásit');
			$visit->logOut();
			$this->orm->persistAndFlush($visit);
			$this->flashMessage('Byl jste odhlášen z termínu');
		}

		$this->redirect('default');
	}

	public function renderPersons() {
		$this->template->groups = $this->person->groups;
	}

	protected function createComponentVisitForm() {
		$form = new Form;

		$form->addRadioList('type', 'Typ vyšetření', [Visit::TYPE_ECG => 'Sportovní prohlídka', Visit::TYPE_SPIRO => 'Funkční vyšetření'])
			->setDefaultValue(Visit::TYPE_ECG)
			->getSeparatorPrototype()->setName(NULL);

		$form->addTextArea('note', 'Poznámka', 50)
			->setNullable();
		$form->addSubmit('ok', 'Uložit');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();

			$visit = $this->person->getNextVisit();
			$visit->note = $values->note;
			$visit->type = $values->type;
			$this->orm->persistAndFlush($visit);

			$this->flashMessage('Detaily vyšetření byly uloženy');
			$this->redirect('default');
		};

		return $form;
	}
}