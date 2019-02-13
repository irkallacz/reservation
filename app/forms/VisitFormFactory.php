<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 16.10.2018
 * Time: 11:20
 */

namespace App\Forms;

use App\Model\Orm\Visit;
use Nette\Application\UI\Form;
use Nette\SmartObject;

final class VisitFormFactory
{
	use SmartObject;

	public static function create():Form
	{
		$form = new Form();

		$form->addRadioList('type', 'Typ vyšetření', [Visit::TYPE_ECG => 'Sportovní prohlídka', Visit::TYPE_SPIRO => 'Funkční vyšetření'])
			->setDefaultValue(Visit::TYPE_ECG)
			->getSeparatorPrototype()->setName(NULL);

		$form->addTextArea('note', 'Poznámka', 50)
			->setNullable();

		return $form;
	}

}