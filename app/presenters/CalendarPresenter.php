<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 23.2.2017
 * Time: 11:36
 */

namespace App\Presenters;

use Nette\Application\BadRequestException;
use Nette\Utils\ArrayHash;
use Nextras\Dbal\Connection;
use Tracy\Debugger;

final class CalendarPresenter extends BasePresenter
{
	/**
	 * @var Connection @inject
	 */
	public $dbal;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * CalendarPresenter constructor.
	 * @param string $password
	 */
	public function __construct(string $password)
	{
		parent::__construct();
		$this->password = $password;
	}

	/**
	 * @param string $password
	 * @throws BadRequestException
	 */
	public function actionDefault(string $password)
    {
        if ($password != $this->password){
            throw new BadRequestException();
        }
    }

	/**
	 * @param string $password
	 * @throws \Nextras\Dbal\QueryException
	 */
	public function renderDefault(string $password)
    {
		$visits = $this->orm->visits->findBy(['this->person!=' => NULL]);
        $this->template->visits = $visits;
        $this->template->date = new \DateTime;

		$groups = $this->dbal->query('SELECT DISTINCT [title], DATE([date_start])AS [day] FROM [visits] JOIN [groups] ON [groups.id] = [group_id] WHERE [group_id] IS NOT NULL ORDER BY [date_start]')
			->fetchPairs('day', 'title');

		$days = [];
		foreach ($groups as $date => $group){
			$date = new \DateTimeImmutable($date);
			$day = $date->format('Y-m-d');
			$days[$day]['start'] = $date->format('Ymd');
			$days[$day]['end'] = $date->modify('+ 1 day')->format('Ymd');
			$days[$day]['group'] = $group;
		}

		$this->template->days = ArrayHash::from($days);
    }
}