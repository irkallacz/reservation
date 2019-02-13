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
use Tracy\Debugger;


final class UserFormFactory
{
	use Nette\SmartObject;

	/**
	 * @return Form
	 */
	public static function create(): Form
	{

		$form = new Form;

		$form->addText('surname', 'Příjmení:')
			->setRequired('Vyplňte prosím příjmení')
			->addFilter(['\Nette\Utils\Strings', 'firstUpper']);

		$form->addText('name', 'Jméno:')
			->setRequired('Vyplňte prosím jméno')
			->addFilter(['\Nette\Utils\Strings', 'firstUpper']);

		$form->addText('phone', 'Telefon:', 9, 9)
			->setType('tel')
			->setRequired('Vyplňte prosím telefon')
			->addCondition(Form::FILLED)
			->addRule(Form::INTEGER, 'Telefonní číslo musí být složeno z číslic')
			->addRule(Form::LENGTH, 'Telefonní číslo musí mít %d číslic', 9);

		$form->addText('mail', 'E-mail:', 40)
			->setType('email')
			->setEmptyValue('@')
			->setRequired('Vyplňte prosím e-mail')
			->addFilter(['\Nette\Utils\Strings', 'lower'])
			->addRule(Form::EMAIL, 'Neplatný e-mail');

		$form->addProtection('Vypršel časový limit, odešlete prosím formulář znovu');

		return $form;
	}

}