<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 14.03.2018
 * Time: 13:46
 */

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use Nette\Http\UserStorage;
use Nette\Utils\ArrayHash;

/**
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
 */
abstract class AdminPresenter extends BasePresenter
{

	public function checkRequirements($element)
	{
		$this->user->getStorage()->setNamespace('admin');

		if (!$this->user->isLoggedIn()) {
			if ($this->user->logoutReason === UserStorage::INACTIVITY) {
				$this->flashMessage('Byl jste odhlášen z důvodu dlouhé neaktivity', 'error');
			}
			$backlink = $this->storeRequest();
			$this->redirect('Sign:', ['backlink' => $backlink]);
		}

		parent::checkRequirements($element);
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$menu = [
			['title' => 'Administrace', 'link' => 'Homepage:',	'current' => 'Homepage:*',	'role'=> 'admin', 	'icon' => 'home'    ],
			['title' => 'Pacienti', 	'link' => 'Patient:',	'current' => 'Patient:*', 	'role'=> 'admin',	'icon' => 'person'	],
			['title' => 'Skupiny', 		'link' => 'Group:',		'current' => 'Group:*', 	'role'=> 'admin',	'icon' => 'group'	],
			['title' => 'Žádosti', 		'link' => 'Volunteer:',	'current' => 'Volunteer:*', 'role'=> 'admin',	'icon' => 'request'	],
		];

		$this->template->menu = ArrayHash::from($menu);
	}
}