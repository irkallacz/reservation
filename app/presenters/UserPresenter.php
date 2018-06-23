<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 07.02.2018
 * Time: 10:48
 */

namespace App\Presenters;

use App\Model\Orm\Person;
use Nette\Http\UserStorage;

abstract class UserPresenter extends BasePresenter
{

	/** @var Person */
	protected $person;

	public function startup()
	{
		parent::startup();
		$this->person = $this->orm->persons->getById($this->user->id);
	}

	public function checkRequirements($element)
	{
		$this->user->getStorage()->setNamespace('user');

		if (!$this->user->isLoggedIn()) {
			if ($this->user->logoutReason === UserStorage::INACTIVITY) {
				$this->flashMessage('Byl jste odhlášen z důvodu dlouhé neaktivity', 'error');
			}
			$backlink = $this->storeRequest();
			$this->redirect('Sign:', ['backlink' => $backlink]);
		}

		parent::checkRequirements($element);
	}

}