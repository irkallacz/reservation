<?php //SignPresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class SignPresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Sign:default');
	}

	public function testSignForm(){
		$this->checkForm('Sign:default', 'signForm', [
			'mail' => 'tomas.blby@centrum.cz',
			'password' => 'tomas.blby@centrum.cz',
		], '/reservation/');
	}

	public function testRenderRegister()
	{
		$this->checkAction('Sign:register');
	}

	public function testRegisterForm(){
		$this->checkForm('Sign:register', 'registrationForm', [
			'mail' => 'novy.patrik@seznam.cz',
			'phone' => '609112777',
			'name' => 'Patrik',
			'surname' => 'Nový',
			'check' => TRUE,
		], '/sign/');
	}


	public function testRenderRecoverPassword()
	{
		$this->checkAction('Sign:recoverPassword');
	}

	public function testRecoverPasswordForm(){
		$this->checkForm('Sign:recoverPassword', 'recoverPasswordForm', [
			'mail' => 'tomas.blby@centrum.cz',
		], '/sign/');
	}


	public function testRenderSouhlas()
	{
		$this->checkAction('Sign:souhlas');
	}

}

(new SignPresenterTest())->run();