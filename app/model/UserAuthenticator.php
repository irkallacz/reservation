<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jakub
 * Date: 24.7.15
 * Time: 0:02
 * To change this template use File | Settings | File Templates.
 */

namespace App\Model;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Tracy\Debugger;

class UserAuthenticator implements IAuthenticator {

	/**
	 * @var Orm
	 */
	private $orm;

	/**
	 * MyAuthenticator constructor.
	 * @param Orm $orm
	 */
	public function __construct(Orm $orm) {
		$this->orm = $orm;
	}

	/**
	 * @param array $credentials
	 * @return Identity|\Nette\Security\IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials){
		list($mail, $password) = $credentials;

		$person = $this->orm->persons->getByMail($mail);

		if (!$person) {
			throw new AuthenticationException('Osoba s tímto e-mailem nenalezena');
		}

		if (!Passwords::verify($password, $person->password)) {
			throw new AuthenticationException('Chybné heslo');
		}

		$person->dateUpdate = new \DateTimeImmutable();

		$this->orm->persistAndFlush($person);

		return new Identity($person->id, 'user');
	}
}
