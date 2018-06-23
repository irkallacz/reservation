<?php
/**
 * Created by PhpStorm.
 * User: Jakub
č * Date: 07.02.2018
 * Time: 10:48
 */

namespace App\AdminModule\Presenters;


use App\Forms\SignFormFactory;
use App\Model\AdminAuthenticator;
use App\Presenters\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

final class SignPresenter extends BasePresenter
{

	/** @var AdminAuthenticator @inject*/
	public $authenticator;

	/** @var $backlink */
	public $backlink = '';

	public function actionOut()
	{
		$this->getUser()->logout();

		$this->flashMessage('Byl jste odhlášen');
		$this->redirect('Reservation:default');
	}

	public function actionDefault()
	{
		$this->user->getStorage()->setNamespace('admin');

		if ($this->user->isLoggedIn())
		{
			$this->redirect('Reservation:default');
		}
	}

	protected function createComponentSignForm()
	{
		$form = SignFormFactory::create();

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();

			try {
				$this->user->getStorage()->setNamespace('admin');
				$this->user->setAuthenticator($this->authenticator);
				$this->user->login($values->mail, $values->password);
				$this->user->setExpiration(0, TRUE);
				$this->restoreRequest($this->backlink);
				$this->redirect('Reservation:default');

			} catch (AuthenticationException $e) {
				$form->addError($e->getMessage());
			}

		};

		return $form;
	}

}