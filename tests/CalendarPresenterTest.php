<?php //SignPresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class CalendarPresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	public function testRenderDefault()
	{
		$this->checkAction('Calendar:default');
	}

	public function testRenderDefaultWithPassword()
	{
		$this->checkAction('Calendar:default', ['password' => 'iZ2LrcLnQ9']);
	}

}

(new CalendarPresenterTest())->run();