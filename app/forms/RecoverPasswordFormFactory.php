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
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Tracy\Debugger;


final class RecoverPasswordFormFactory
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
			->addFilter(['\Nette\Utils\Strings', 'lower'])
			->addRule(Form::EMAIL,'Neplatný e-mail');

		$form->addSubmit('ok', 'OK');

		$form->addProtection('Vypršel časový limit, odešlete prosím formulář znovu');

		return $form;
	}

}