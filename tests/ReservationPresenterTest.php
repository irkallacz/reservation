<?php //ReservationPresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class ReservationPresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function setUp()
	{
		$this->logIn(1);
	}

	public function testRenderDefault()
	{
		$this->checkAction('Reservation:default');
	}

	public function testRenderPerson()
	{
		$this->checkAction('Reservation:persons');
	}
	
	public function testActionLogIn()
	{
		$this->checkRedirect('Reservation:logIn', '/reservation/', ['id' => 1]);
	}

	/**
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function testActionLogInClose()
	{
		$this->checkAction('Reservation:logIn', ['id' => 2]);
	}

	/**
	 * @throws \Nette\Application\ForbiddenRequestException
	 */
	public function testActionLogInWrong()
	{
		$this->checkAction('Reservation:logIn', ['id' => 4]);
	}

}

(new ReservationPresenterTest())->run();