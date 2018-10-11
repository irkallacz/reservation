<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 14.03.2018
 * Time: 13:46
 */

namespace App\AdminModule\Presenters;

use Nette\Http\UserStorage;
use Nette\Utils\ArrayHash;

/**
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
 */
abstract class AdminPresenter extends BasePresenter
{

	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
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