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


final class PatientFormFactory
{
	use Nette\SmartObject;

	/**
	 * @return Form
	 */
	public static function create(): Form
	{

		$form = UserFormFactory::create();

		$form->addText('address', 'Adresa:', 40)
			->setNullable();

		$form->addText('rc', 'Rodné číslo:', 11)
			->setAttribute('placeholder', '______/____')
			->setOption('description', '(000000/0000)')
			->setNullable()
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Jste si jistí rodným číslem?', '[0-9]{2}[0156][0-9][0-3][0-9]/[0-9]{3,4}');


		return $form;
	}

}