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

		$form->addText('phone', 'Telefon:', 9, 9)
			->setType('tel')
			->setRequired('Vyplňte prosím telefon')
			->addCondition(Form::FILLED)
			->addRule(Form::LENGTH, 'Telefon musí mít %d číslic', 9);

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