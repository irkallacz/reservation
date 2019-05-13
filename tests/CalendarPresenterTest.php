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
		$response = $this->check('Calendar:default', ['password' => 'iZ2LrcLnQ9']);
		\Tester\Assert::type(Nette\Application\Responses\TextResponse::class, $response);
	}

}

(new CalendarPresenterTest())->run();