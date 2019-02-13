<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 4.2.2018
 * Time: 11:54
 */

namespace App\Presenters;

use App\Forms\VisitFormFactory;
use App\Model\Email;
use App\Model\Orm\Visit;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

/**
 * Class ReservationPresenter
 * @package App\Presenters
 */
final class ReservationPresenter extends UserPresenter
{

	/** @var IMailer $mailer @inject */
	public $mailer;

	/**
	 *
	 */
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
	 * @param int $id
	 * @throws BadRequestException
	 * @throws ForbiddenRequestException
	 * @throws AbortException
	 */
	public function actionLogIn(int $id) {
		$visit = $this->orm->visits->getById($id);

		if (!$visit) throw new BadRequestException('Termín nenalezen');
		if (!$visit->canLogIn) throw new ForbiddenRequestException('Na termín se již nedá přihlásit');
		if (($visit->group) and (!$this->person->groups->has($visit->group))) throw new ForbiddenRequestException('Tento termín nepatří žádné vaší skupině');

		$nextVisit = $this->person->getNextVisit();
		if ($nextVisit) {
			$this->person->visits->remove($nextVisit);
			$nextVisit->logOut();
			$this->flashMessage('Vaše rezervace na termín ' . $nextVisit->dateStart->format('d.m.Y \v H:i'). ' byla zrušena', 'notice');
			$this->orm->persistAndFlush($nextVisit);
		}

		$visitRequest = $this->person->visitRequest;
		if ($visitRequest) {
			$this->flashMessage('Vaše žádost o vyšetření byla zrušena', 'notice');
			$this->orm->remove($visitRequest);
		}

		$visit->person = $this->person;
		$visit->open = FALSE;
		$this->orm->visits->persistAndFlush($visit);

		$mail = Email::newMessage();
		$mail->addTo($this->person->mail, $this->person->fullName);
		$mail->setSubject('Rezervace prohlídky '.$visit->dateStart->format('d.m.Y \v H:i'));

		$template = $this->createTemplate();
		$template->setFile(__DIR__.'/templates/Email/reservation.latte');
		$template->date = $visit->dateStart;
		$mail->setBody($template);

		$template = $this->createTemplate();
		$template->setFile(__DIR__.'/templates/Email/event.latte');
		$template->visit = $visit;
		$event = (string) $template;
		$event = str_replace("\n","\r\n", $event);
		$mail->addAttachment('event.ics', $event);

		$this->mailer->send($mail);

		unset($event);
		unset($template);
		unset($mail);

		$this->flashMessage('Rezervovali jste si termín na ' . $visit->dateStart->format('d.m.Y \v H:i'));
		$this->redirect('default');
	}

	/**
	 * @throws ForbiddenRequestException
	 * @throws AbortException
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

	/**
	 *
	 */
	public function renderPersons() {
		$this->template->groups = $this->person->groups;
	}

	/**
	 * @return Form
	 */
	protected function createComponentVisitForm(): Form
	{
		$form = VisitFormFactory::create();

		$form->addSubmit('ok', 'Uložit');

		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
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