<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 15.10.2018
 * Time: 13:11
 */

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\SmartObject;
use Nextras\Forms\Controls\Typeahead;

final class SearchUserFactory
{
	use SmartObject;

	public static function create():Form
	{
		$form = new Form();

		$form['query'] = new Typeahead();
		$form->addHidden('id')
			->setHtmlId('patient-id');

		$form->addCheckbox('send', 'Poslat email');

		$form->addSubmit('ok', 'OK');

		return $form;
	}

}