<?php

namespace App\Presenters;

use Nette\Security\Passwords;
use Nextras\Orm\Collection\ICollection;
use Tracy\Debugger;

final class HomepagePresenter extends BasePresenter
{
	public function renderDefault()
	{
		$groups = $this->orm->groups
			->findBy(['active' => TRUE])
			->orderBy('title');
		$this->template->groups = $groups;

		$freeVisits = $this->orm->visits->findFutureByGroup(NULL)
			->resetOrderBy()->orderBy('dateLimit', ICollection::DESC);

		$this->template->firstFreeVisit = $freeVisits->fetch();
		$this->template->freeVisitCount = $freeVisits->count();

	}
}
