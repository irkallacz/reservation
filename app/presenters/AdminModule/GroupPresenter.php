<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 20.03.2018
 * Time: 13:35
 */

namespace App\AdminModule\Presenters;


use App\Forms\GroupFormFactory;
use App\Model\Orm\Group;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Security\Passwords;
use Nextras\Orm\Collection\ICollection;
use Symfony\Component\Debug\Debug;
use Tracy\Debugger;

final class GroupPresenter extends AdminPresenter {

	public function renderDefault()
	{
		$this->template->groups = $this->orm->groups
			->findAll()
			->orderBy('active', ICollection::DESC)
			->orderBy('title');
	}

	protected function createComponentGroupForm()
	{
		return new Multiplier(function ($id)
		{
			$form = GroupFormFactory::create();

			$renderer = $form->getRenderer();
			$renderer->wrappers['controls']['container'] = 'tr';
			$renderer->wrappers['pair']['container'] = 'td';
			$renderer->wrappers['label']['container'] = NULL;
			$renderer->wrappers['control']['container'] = NULL;

			$form['title']->caption = NULL;
			$form['active']->caption = NULL;
			$form['password']->caption = NULL;

			$group = $this->orm->groups->getById($id);
			if ($group) $form->setDefaults($group->toArray());

			$form->onSuccess[] = [$this, 'successGroupForm'];
			return $form;
		});
	}

	protected function createComponentAddGroupForm()
	{
		$form = GroupFormFactory::create();
		$form->onSuccess[] = [$this, 'successGroupForm'];
		return $form;
	}

	public function successGroupForm(Form $form)
	{
		$values = $form->getValues();

		if ($values->password) $values->password = Passwords::hash($values->password);

		if ($values->id) {
			$group = $this->orm->groups->getById($values->id);
			$this->flashMessage('Skupina byla uložena');
		} else {
			$group = new Group();
			$this->flashMessage('Nová skupina byla vytvořena');
		}

		if ($group) {
			$group->active = $values->active;
			$group->title = $values->title;
			$group->title = $values->title;
			if ($values->password) $group->password = $values->password;
		}

		$this->orm->persistAndFlush($group);

		$this->redirect('this');

	}



}