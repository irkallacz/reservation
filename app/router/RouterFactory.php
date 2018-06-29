<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		$router[] = new Route('admin/reservation/<year \d{4}>/<week \d{1,2}>', [
			'presenter' => 'Reservation',
			'action' => 'default',
			'module' => 'Admin'
		]);

		$router[] = new Route('admin/<presenter>/<action>[/<id>]', [
			'presenter' => 'Reservation',
			'action' => 'default',
			'module' => 'Admin'
		]);

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}
}
