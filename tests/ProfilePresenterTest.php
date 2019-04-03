<?php //ProfilePresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class ProfilePresenterTest extends \Tester\TestCase
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
			'rc' => '123456/0000',
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
			'password' => 'tomas.blby@centrum.cz',
			'confirm' => 'tomas.blby@centrum.cz',
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

}

(new ProfilePresenterTest())->run();