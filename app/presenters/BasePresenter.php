<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 2.2.2018
 * Time: 10:20
 */

namespace App\Presenters;

use App\Model\Orm;
use Nette\Application\UI\Presenter;

/**
 * @property-read \Nette\Bridges\ApplicationLatte\Template|\stdClass $template
 */
abstract class BasePresenter extends Presenter
{
	/** @var Orm @inject */
	public $orm;

}