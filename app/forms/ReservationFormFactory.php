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
use Nextras\Orm\Collection\ICollection;

final class ReservationFormFactory
{
	use Nette\SmartObject;

	/** ICollection */
	private $groups;

	/**
	 * GroupLogInFormFactory constructor.
	 * @param ICollection $groups
	 */
	public function __construct(ICollection $groups)
	{
		$this->groups = $groups;
	}


	/**
	 * @return Form
	 */
	public function create(): Form
	{

		$form = VisitFormFactory::create();

		$groups = $this->groups->fetchPairs('id','title');

		$form->addDatePicker('dateLimit', 'Konec přihlášení:');

		$form->addSelect('group', 'Skupina:')
			->setItems($groups);

		$form->addSubmit('ok', 'Uložit');

		return $form;
	}
}