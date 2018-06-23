<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 30.01.2018
 * Time: 14:39
 */

namespace App\Model;

use App\Model\Orm\GroupsRepository;
use App\Model\Orm\Person;
use App\Model\Orm\PersonsRepository;
use App\Model\Orm\AdminsRepository;
use App\Model\Orm\VisitsRepository;
use App\Model\Orm\VisitRequestsRepository;

/**
 * @property-read VisitsRepository $visits
 * @property-read PersonsRepository $persons
 * @property-read GroupsRepository $groups
 * @property-read VisitRequestsRepository $visitRequests
 * @property-read AdminsRepository $admins
 */
final class Orm extends \Nextras\Orm\Model\Model
{
}