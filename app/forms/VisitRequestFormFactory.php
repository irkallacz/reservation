<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 6.2.2018
 * Time: 18:09
 */

namespace App\Forms;

use App\Model\Orm\VisitRequest;
use Nette\Application\UI\Form;
use Nette\SmartObject;
use Nette\Utils\DateTime;

final class VisitRequestFormFactory
{
	use SmartObject;

	/**
	 * @return Form
	 */
	public static function create(): Form
	{
		$form = new Form();

		$form->addDatePicker('dateStart', 'Od')
			->setRequired('Vyplňte datum začátku intervalu')
			->setDefaultValue(new DateTime('+ 1 day'));

		$form->addDatePicker('dateEnd', 'Do')
			->setRequired('Vyplňte datum konce intervalu')
			->setDefaultValue(new DateTime('+ 8 day'));

		$form->addCheckboxList('daysArray', 'Jen ve dnech', VisitRequest::DAY_NAMES)
			->setDefaultValue(array_keys(VisitRequest::DAY_NAMES))
			->getSeparatorPrototype()
			->setName(NULL);

		$form->addCheckbox('active', 'Aktivní')
			->setDefaultValue(TRUE);

		$form->addSubmit('ok', 'Uložit');

		$form->onValidate[] = function (Form $form){
			$values = $form->getValues();
			$now = new DateTime();

			if ($values->dateStart > $values->dateEnd) {
				$form->addError('Datum začátku musí být menší než datum konce');
			}

			if (($values->active)and(($values->dateStart < $now)or($values->dateEnd < $now))) {
				$form->addError('Datum začátku a konce musí ležet v budoucnosti');
			}

		};

		return $form;
	}
}