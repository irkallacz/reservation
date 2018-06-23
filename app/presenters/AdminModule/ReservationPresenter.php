<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.02.2018
 * Time: 13:38
 */

namespace App\AdminModule\Presenters;

use App\Forms\VisitFormFactory;
use App\Model\Orm\Visit;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\Button;
use Nette\Utils\DateTime;
use Tracy\Debugger;

final class ReservationPresenter extends AdminPresenter {

	/**
	 * @param int $year
	 * @param int $week
	 */
	public function renderDefault($year = NULL, $week = NULL) {
		if (is_null($year)) $year = intval(date('Y'));
		if (is_null($week)) $week = intval(date('W'));

		$this->template->year = $year;
		$this->template->week = $week;

		$dateStart = new DateTime();
		$dateStart->setISODate($year, $week);
		$dateStart->setTime(8, 0, 0);
		$dateEnd = $dateStart->modifyClone('+6 day');

		$this->template->next = $dateStart->modifyClone('+ 1 week');
		$this->template->prev = $dateStart->modifyClone('- 1 week');

		$days = new \DatePeriod($dateStart, new \DateInterval('P1D'), 4);

		$formatHour = 'd.m.Y H:i';
		$formatDay = 'd.m.Y';

		$calendar = [];
		$events = [];
		foreach ($days as $date) {
			/* @var Datetime $date*/
			for ($i = 0; $i <= 7; $i++){
				$calendar[$date->format($formatDay)][$date->format($formatHour)] = NULL;
				$events[] = $date->format($formatHour);
				$date->modify('+ 45 minute');
				if ($i == 4) $date->modify('+1 hour 15 minute');
			}
		}

		$visits = $this->orm->visits->findBy([
			'dateStart>=' => $dateStart,
			'dateEnd<=' => $dateEnd
		])->orderBy('dateStart')
			->fetchPairs('id');

		foreach ($visits as $visit){
			if (array_key_exists($visit->dateStart->format($formatHour), $calendar[$visit->dateStart->format($formatDay)]))
			{
				$calendar[$visit->dateStart->format($formatDay)][$visit->dateStart->format($formatHour)] = $visit;
			}
		}

		$this->template->dateStart = $dateStart;
		$this->template->calendar = $calendar;
		$this->template->hours = array_keys($calendar[array_keys($calendar)[0]]);

		$this['reservationForm']['visit']->setItems(array_flip(array_keys($visits)));
		$this['reservationForm']['event']->setItems($events);

		$now = new DateTime();
		$top = (intval($now->format('H'))-7) * 4 + intval($now->format('i')) / 15;

		$this->template->top = $top;
	}

	protected function createComponentReservationForm()
	{
		$visitFormFactory = new VisitFormFactory(
		 	$this->orm->groups->findAll()->orderBy('title')
		);
		$form = $visitFormFactory->create();

		$form->addCheckbox('open', 'Otevřeno')
			->setDefaultValue(TRUE);

		$form->addCheckboxList('event')
			->setOmitted();

		$form->addCheckboxList('visit')
			->setOmitted();

		$form['group']->setPrompt('');

		$form->addSubmit('delete', 'Smazat')
			->onClick[] = function (Button $button){
				$form = $button->getForm();
				$visits = $form->getHttpData($form::DATA_TEXT | $form::DATA_KEYS, 'visit[]');
				foreach ($visits as $id){
					$visit = $this->orm->visits->getById($id);
					$this->orm->remove($visit);
				}
				$this->orm->flush();
				$this->flashMessage('Termíny smazány');
				$this->redirect('this');
		};

		$form->addSubmit('change', 'Změnit')
			->onClick[] = function (Button $button){
				$form = $button->getForm();
				$values = $form->getValues();
				$visits = $form->getHttpData($form::DATA_TEXT | $form::DATA_KEYS, 'visit[]');
				foreach ($visits as $id){
					$visit = $this->orm->visits->getById($id);
					$visit->dateLimit = $values->dateLimit ? $values->dateLimit : $visit->dateLimit;
					$visit->group = $values->group;
					$visit->open = $values->open;
					$this->orm->persist($visit);
				}
				$this->orm->flush();
				$this->flashMessage('Termíny změněny');
				$this->redirect('this');
		};

		$form->addSubmit('add', 'Přidat')
			->onClick[] = function (Button $button){
				$form = $button->getForm();
				$values = $form->getValues();
				$events = $form->getHttpData($form::DATA_TEXT | $form::DATA_KEYS, 'event[]');

				foreach ($events as $date){
					$visit = new Visit();
					$visit->dateStart = new \DateTimeImmutable($date);
					$visit->dateEnd = $visit->dateStart->modify('+ 45 minute');
					$visit->dateLimit = $values->dateLimit ? $values->dateLimit : $visit->dateStart->modify('- 8 day');;
					$visit->open = $values->open;
					$this->orm->persist($visit);
					$visit->group = $values->group;
					$this->orm->persist($visit);
				}
				$this->orm->flush();
				$this->flashMessage('Termíny vytvořeny');
				$this->redirect('this');
			};

		return $form;
	}

	public function renderView($id)
	{
		$visit = $this->orm->visits->getById($id);
		$this->template->visit = $visit;

		if ($visit->group) $this['visitForm']['group']->setDefaultValue($visit->group->id);
		$this['visitForm']['dateLimit']->setDefaultValue($visit->dateLimit);
	}

	public function actionDelete($id)
	{
		$visit = $this->orm->visits->getById($id);

		$this->orm->visits->remove($visit);
		$this->orm->visits->flush();

		$this->flashMessage('Termín by smazán');
		$this->redirect('default');
	}

	public function actionSetOpen($id, $open)
	{
		$visit = $this->orm->visits->getById($id);
		$visit->open = $open;
		$this->orm->visits->persistAndFlush($visit);

		$this->flashMessage(($open) ? 'Termín byl otevřen' : 'Termín byl uzavřen');
		$this->redirect('view', $visit->id);
	}

	public function actionLogout($id)
	{
		$visit = $this->orm->visits->getById($id);
		$visit->logOut();
		$this->orm->visits->persistAndFlush($visit);

		$this->flashMessage('Osoba odhlášena z termínu');
		$this->redirect('view', $visit->id);
	}

	protected function createComponentVisitForm()
	{
		$visitFormFactory = new VisitFormFactory(
			$this->orm->groups->findAll()->orderBy('title')
		);
		$form = $visitFormFactory->create();

		$form['group']->setPrompt('');

		$form->addSubmit('ok', 'Uložit');

		$form->onSuccess[] = function (Form $form){
			$values = $form->getValues();
			$id = $this->getParameter('id');

			$visit = $this->orm->visits->getById($id);
			$visit->dateLimit = $values->dateLimit;
			$visit->group = $values->group;
			$this->orm->visits->persistAndFlush($visit);

			$this->flashMessage('Termín byl aktualizován');
			$this->redirect('this');

		};

		return $form;
	}

	protected function createComponentCalendarForm()
	{
		$year = $this->getParameter('year');
		$year = $year ? $year : date('Y');
		$years = range(($year - 3), ($year + 3));
		$years = array_combine($years, $years);

		$week = $this->getParameter('week');
		$week = $week ? $week : date('W');

		$date = new DateTime();
		$date->setISODate($year, $week);

		$months = range(1,12);
		$months = array_combine($months, $months);

		foreach ($months as $month){
			$months[$month] = strftime('%B', date_create("1.$month.2000")->format('U'));
		}

		$form = new Form;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = NULL;
		$renderer->wrappers['label']['container'] = NULL;
		$renderer->wrappers['control']['container'] = NULL;

		$form->addSelect('month')
			->setItems($months)
			->setDefaultValue($date->format('n'));

		$form->addSelect('year')
			->setItems($years)
			->setDefaultValue($year);

		$form->addSubmit('ok', 'Přejít');

		$form->onSuccess[] = function (Form $form){
			$values = $form->getValues();

			$date = new DateTime("1.$values->month.$values->year");
			if ($date->format('N')>5) $date->modify('+ 2 day');
			$week = $date->format('W');

			$this->redirect('this', ['year' => $values->year, 'week' => $week]);
		};

		return $form;
	}
}