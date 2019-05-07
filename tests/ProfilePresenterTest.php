<?php //ProfilePresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class ProfilePresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function setUp()
	{
		$this->logIn(1);
	}

	public function testRenderDefault()
	{
		$this->checkAction('Profile:default');
	}

	public function testRenderEdit()
	{
		$this->checkAction('Profile:edit');
	}

	public function testUserForm()
	{
		$this->checkForm('Profile:edit', 'userForm', [
			'name' => 'Tomáš',
			'surname' => 'Blbý',
			'rc' => '120416/0008',
			'mail' => 'tomas.blby@centrum.cz',
			'phone' => '609112777',
			'adress' => 'Horni dolní',
		], '/profile/');
	}

	public function testRenderPassword()
	{
		$this->checkAction('Profile:password');
	}

	public function testPasswordForm()
	{
		$this->checkForm('Profile:edit', 'passwordForm', [
			'password' => 'tomas.blby',
			'confirm' => 'tomas.blby',
		], '/profile/');
	}


	public function testActionGroupLogIn()
	{
		$this->checkAction('Profile:groupLogIn');
	}


	public function testGroupForm()
	{
		$this->checkForm('Profile:groupLogIn', 'groupForm', [
			'group' => '2',
			'password' => 'Skupina 2',
		], '/profile/');
	}

	public function testActionGroupLogOut()
	{
		$this->checkRedirect('Profile:groupLogOut', '/profile/', ['id' => 1]);
	}

	/**
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function testActionWrongGroupLogOut()
	{
		$this->checkAction('Profile:groupLogOut', ['id' => 3]);
	}


	public function testActionVisitRequest()
	{
		$this->checkAction('Profile:visitRequest');
	}

	public function testVisitRequestForm()
	{
		$this->checkForm('Profile:visitRequest', 'visitRequestForm', [
			'dateStart' => date_create('+1 week')->format('Y-m-d'),
			'dateEnd' => date_create('+2 week')->format('Y-m-d'),
			'daysArray' => array_keys(\App\Model\Orm\VisitRequest::DAY_NAMES),
			'active' => TRUE,
		], '/profile/');
	}

}

(new ProfilePresenterTest())->run();