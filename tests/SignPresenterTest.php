<?php //SignPresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class SignPresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Sign:default');
	}

	public function testRenderRegister()
	{
		$this->checkAction('Sign:register');
	}

	public function testRenderRecoverPassword()
	{
		$this->checkAction('Sign:recoverPassword');
	}

	public function testRenderSouhlas()
	{
		$this->checkAction('Sign:souhlas');
	}

}

(new SignPresenterTest())->run();