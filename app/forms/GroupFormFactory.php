<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jakub
 * Date: 9.4.15
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

namespace App\Forms;

use Nette\Application\UI\Form;
use Tracy\Debugger;


final class GroupFormFactory {

	/**
	 * @return Form
	 */
	public static function create(): Form
	{

		$form = new Form;
		$form->addText('title','Název');

		$form->addPassword('password','Heslo');

		$form->addCheckbox('active','Aktivní');

		$form->addSubmit('ok','Uložit');

		$form->addHidden('id');

		return $form;
	}



}