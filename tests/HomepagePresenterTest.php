<?php //HomepagePresenterTest.php

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
final class HomepagePresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Homepage:default');
	}

}

(new HomepagePresenterTest())->run();