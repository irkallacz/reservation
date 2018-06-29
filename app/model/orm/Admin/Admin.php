<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.1.2018
 * Time: 21:38
 */

namespace App\Model\Orm;

use Nextras\Orm\Entity\Entity;
use DateTimeImmutable;
use Nextras\Orm\Relationships\ManyHasMany;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Admin
 *
 * @property int                    $id				{primary}

 * @property string      			$name
 * @property string      			$surname
 * @property bool      				$liane

 * @property string      			$mail
 * @property string      			$password

 * @property DateTimeImmutable      $dateUpdate

 * @property-read string      		$fullName 		{virtual}
 */
final class Admin extends Entity
{
	/**
	 * @return string
	 */
	protected function getterFullName(): string
	{
		return $this->surname . ' ' . $this->name;
	}

}
