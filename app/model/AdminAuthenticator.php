<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jakub
 * Date: 24.7.15
 * Time: 0:02
 * To change this template use File | Settings | File Templates.
 */

namespace App\Model;

use Nette\Application\ApplicationException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

class AdminAuthenticator implements IAuthenticator {

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
	 * @throws ApplicationException
	 */
	function authenticate(array $credentials){
		list($mail, $password) = $credentials;

		$user = $this->orm->admins->getByMail($mail);

		if (!$user) {
			throw new AuthenticationException('Osoba s tímto účtem nenalezena');
		}

		if ($user->liane) {
			$parts = explode('@', $mail);
			$info = $this->ldap($parts[0], $password);

		}elseif (!Passwords::verify($password, $user->password)) {
			throw new AuthenticationException('Chybné heslo');
		}

		return new Identity($user->id, 'admin', $user->toArray());
	}

	/**
	 * @param string $login
	 * @param string $password
	 * @return ArrayHash
	 * @throws ApplicationException
	 * @throws AuthenticationException
	 */
	private function ldap($login, $password)
	{
		$ds = ldap_connect('ldaps://ldap.tul.cz/', 636);

		if ($ds) {
			$name = str_replace('.', '_', $login);
			$rdn = "cn=$name,ou=liane,o=vslib";

			if (@ldap_bind($ds, $rdn, $password)) {
				$sr = ldap_search($ds, $rdn,"(uid=$login*)", ['sn', 'givenname', 'mail', 'uid']);
				$info = ldap_get_entries($ds, $sr);
				ldap_close($ds);

				return ArrayHash::from($info[0]);

			} else throw new AuthenticationException('Chybné jméno nebo heslo');

		} else throw new ApplicationException('Nemůžeme se připojit k LDAP serveru');
	}
}
