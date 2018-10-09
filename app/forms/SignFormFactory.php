<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jakub
 * Date: 9.4.15
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

namespace App\Forms;

use Nette;
use	Nette\Application\UI\Form;
use	Tracy\Debugger;


class SignFormFactory
{
	use Nette\SmartObject;

	/**
	 * @return Form
	 */
	public static function create(): Form
	{

		$form = new Form;

		$form->addText('mail', 'E-mail:', 30)
			->setType('email')
			->setEmptyValue('@')
			->setAttribute('autofocus')
			->setRequired('Vyplňte prosím e-mail')
			->addRule(Form::EMAIL,'Neplatný e-mail');

		$form->addPassword('password','Heslo:', 30)
			->setRequired('Heslo nesmí být prázné');

		$form->addSubmit('ok', 'OK');

		$form->addProtection('Vypršel časový limit, odešlete prosím formulář znovu');

		return $form;
	}

}