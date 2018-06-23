<?php
/**
 * Created by PhpStorm.
 * User: Jakub
č * Date: 07.02.2018
 * Time: 10:48
 */

namespace App\Presenters;


use App\Forms\RecoverPasswordFormFactory;
use App\Forms\SignFormFactory;
use App\Forms\UserFormFactory;
use App\Model\Email;
use App\Model\Orm\Admin;
use App\Model\Orm\Person;
use App\Model\UserAuthenticator;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use Nette\Utils\Html;
use Nette\Utils\Random;

final class SignPresenter extends BasePresenter
{

	/** @var UserAuthenticator @inject*/
	public $authenticator;

	/** @var $backlink */
	public $backlink = '';

	/** @var IMailer @inject*/
	public $mailer;

	public function actionLogout()
	{
		$this->getUser()->logout();

		$this->flashMessage('Byl jste odhlášen');
		$this->redirect('Homepage:default');
	}

	protected function createComponentSignForm()
	{
		$form = SignFormFactory::create();

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();

			try {
				$this->user->getStorage()->setNamespace('user');
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

	protected function createComponentRegistrationForm()
	{
		$form = UserFormFactory::create();

		$form->addCheckbox('check', Html::el()->setHtml('Souhlasím s <a href="souhlas">zpracováním osobních údajů'))
			->setOmitted()
			->setRequired('Musíte souhlasit se zpracováním osobních údajů')
			->setDefaultValue(TRUE);

		$form->onValidate[] = function (Form $form)
		{
			$values = $form->getValues();

//			$person = $this->orm->persons->getByRc($values->rc);
//			if ($person) $form->addError('V databázi se již nachází osoba s Vaším rodným číslem!');

			$person = $this->orm->persons->getByMail($values->mail);
			if ($person) $form->addError('V databázi se již nachází osoba s Vaším emailem!');
		};

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();

			$password = Random::generate(8);

			$person = new Person();
			$person->name = $values->name;
			$person->surname = $values->surname;
			$person->mail = $values->mail;
			$person->phone = $values->phone;
//			$person->rc = $values->rc;
//			$person->address = $values->address;
			$person->password = Passwords::hash($password);
			$person->dateUpdate = new \DateTime();
			$this->orm->persistAndFlush($person);

			$mail = Email::newMessage();
			$mail->addTo($person->mail, $person->fullName);
			$mail->setSubject('Registrace do systému rezervace vyšetření');

			$template = $this->createTemplate();
			$template->setFile(__DIR__.'/templates/Email/registration.latte');
			$template->password = $password;
			$mail->setBody($template);

			try
			{
				$this->mailer->send($mail);
			}catch(SendException $e)
			{
				$this->flashMessage('Odeslání e-mailu se nepovedlo, zkuste to prosím znovu', 'error');
				$this->redirect('Sign:register');
			}

			$this->flashMessage('Registrace dokončena. Na Váš email bylo zasláno přihlašovací heslo');
			$this->redirect('Sign:default#prihlaseni');
		};

		return $form;
	}

	protected function createComponentRecoverPasswordForm()
	{
		$form = RecoverPasswordFormFactory::create();

		$form->onValidate[] = function (Form $form)
		{
			$values = $form->getValues();
			$person = $this->orm->persons->getByMail($values->mail);

			if (!$person)
			{
				$form->addError('V databázi se nenachází osoba s Vaším emailem!');
			}
		};

		$form->onSuccess[] = function (Form $form)
		{
			$values = $form->getValues();
			$password = Random::generate(8);
			$hash = Passwords::hash($password);

			$person = $this->orm->persons->getByMail($values->mail);
			$person->mail = $values->mail;

			$mail = new Message();
			$mail->addTo($person->mail, $person->fullName);
			$mail->setFrom('sportovnimedicina@tul.cz','Centrum sportovni medicíny');
			$mail->setSubject('Obnova hesla');

			$template = $this->createTemplate();
			$template->setFile(__DIR__.'/templates/Email/recover-password.latte');
			$template->password = $password;
			$mail->setBody($template);

			try{
				$this->mailer->send($mail);
			}catch(SendException $e) {
				$this->flashMessage('Odeslání e-mailu se nepovedlo, zkuste to prosím znovu', 'error');
				$this->redirect('Sign:register');
			}

			$this->flashMessage('Na Váš email bylo zasláno nové přihlašovací heslo');
			$this->redirect('Sign:default#prihlaseni');
		};

		return $form;
	}
}