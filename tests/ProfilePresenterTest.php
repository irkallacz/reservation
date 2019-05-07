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
			'rc' => '101010/00008',
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

	public function testActionGroupLogOut()
	{
		$this->checkRedirect('Profile:groupLogOut', '/profile/', ['id' => 1]);
	}

	/**
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function testActionWrongGroupLogOut()
	{
		$this->checkAction('Profile:groupLogOut', ['id' => 2]);
	}


	public function testActionVisitRequest()
	{
		$this->checkAction('Profile:visitRequest');
	}

}

(new ProfilePresenterTest())->run();