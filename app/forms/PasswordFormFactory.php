<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 12.02.2019
 * Time: 13:33
 */

namespace App\Forms;


use Nette\Application\UI\Form;
use Nette\SmartObject;

/**
 * Class PasswordForm
 * @package App\Forms
 */
final class PasswordFormFactory
{
	use SmartObject;

	/**
	 * @return Form
	 */
	public static function create():Form
	{
		$form = new Form;

		$form->addProtection('Odešlete prosím formulář znovu');

		$form->addPassword('password', 'Nové heslo', 30)
			->setRequired('Vyplňte heslo')
			->addCondition(Form::FILLED)
			->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 8);


		$form->addPassword('confirm', 'Potvrzení', 30)
			->setOmitted()
			->setRequired('Vyplňte kontrolu hesla')
			->addRule(Form::EQUAL, 'Zadaná hesla se neschodují', $form['password']);

		$form->addSubmit('ok', 'Ulož');

		return $form;
	}

}