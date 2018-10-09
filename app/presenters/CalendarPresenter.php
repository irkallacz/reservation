<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 23.2.2017
 * Time: 11:36
 */

namespace App\Presenters;

use Nette\Application\BadRequestException;

final class CalendarPresenter extends BasePresenter
{
    public function actionDefault($password){
        if ($password != 'ZxnKLplCs'){
            throw new BadRequestException();
        }
    }

    public function renderDefault($password){
		$visits = $this->orm->visits->findBy(['open' => FALSE]);
        $this->template->visits = $visits;
        $this->template->date = new \DateTime;
    }
}