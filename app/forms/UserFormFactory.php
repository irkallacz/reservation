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


class UserFormFactory
{
	use Nette\SmartObject;

	/** @return Form */
	public static function create()
	{

		$form = new Form;

		$form->addText('name', 'Jméno:')
			->setRequired('Vyplňte prosím jméno');

		$form->addText('surname', 'Příjmení:')
			->setRequired('Vyplňte prosím příjmení');

//		$form->addCheckbox('skipControl', 'bez kontroly RČ')
//			->setOmitted()
//			->setDefaultValue(FALSE);
//
//		$form->addText('rc', 'Rodné čílo:', 11)
//			->setAttribute('placeholder', '______/____')
//			->setOption('description', '(000000/0000)')
//			->setRequired('Vyplňte prosím rodné číslo')
//			->addConditionOn($form['skipControl'], Form::EQUAL, FALSE)
//				->addRule(Form::PATTERN, 'Jste si jistí rodným číslem?', '[0-9]{2}[0156][0-9][0-3][0-9]/[0-9]{3,4}');

//		$form->addText('address', 'Adresa:', 40)
//			->setRequired('Vyplňte prosím adresu');

		$form->addText('phone', 'Telefon:', 9)
			->setType('tel')
			->setRequired('Vyplňte prosím telefon');

		$form->addText('mail', 'E-mail:', 40)
			->setType('email')
			->setEmptyValue('@')
			->setRequired('Vyplňte prosím e-mail')
			->addRule(Form::EMAIL, 'Neplatný e-mail');

		$form->addSubmit('ok', 'OK');

		$form->addProtection('Vypršel časový limit, odešlete prosím formulář znovu');

		return $form;
	}

}