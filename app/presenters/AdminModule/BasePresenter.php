<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 2.2.2018
 * Time: 10:20
 */

namespace App\AdminModule\Presenters;

use App\Model\Orm;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;

/**
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
 */
abstract class BasePresenter extends Presenter
{
	/** @var Orm @inject */
	public $orm;

	/**
	 * @param $element
	 * @throws ForbiddenRequestException
	 */
	public function checkRequirements($element)
	{
		//$this->getUser()->getStorage()->setNamespace('admin');
		parent::checkRequirements($element);
	}

}