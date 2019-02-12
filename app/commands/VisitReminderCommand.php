<?php

/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 27.02.2018
 * Time: 13:23
 */

namespace App\Console;

use App\Model\Email;
use App\Model\Orm;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class VisitReminderCommand extends Command {
	/** @var Orm */
	private $orm;

	/** @var IMailer */
	private $mailer;

	/**
	 * VisitReminderCommand constructor.
	 * @param Orm $orm
	 * @param IMailer $mailer
	 */
	public function __construct(Orm $orm, IMailer $mailer) {
		parent::__construct();
		$this->orm = $orm;
		$this->mailer = $mailer;
	}

	/**
	 * @return void
	 */
	protected function configure() {
		$this->setName('visits:reminder');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$day = new \DateTimeImmutable('+3 day');
		$start = $day->setTime(0, 0, 0);
		$end = $day->setTime(23, 0, 0);

		$visits = $this->orm->visits->findBy([
			'dateStart>=' => $start,
			'dateStart<=' => $end,
			'open' => FALSE,
			'person!=' => NULL,
		]);

		$output->writeln('Found '.$visits->count() .' visits witch start within 3 days');

		$latte = new \Latte\Engine;

		foreach ($visits as $visit) {
			$person = $visit->person;

			$mail = Email::newMessage();
			$mail->addTo($person->mail, $person->fullName);
			$mail->setSubject('Připomínka vyšetření');
			$mail->setBody($latte->renderToString(__DIR__ . '/../presenters/templates/Email/reminder.latte', ['date' => $visit->dateStart]));

			$this->mailer->send($mail);

			$output->writeln('Email notification send to '.$person->mail);
		}

		$output->writeln('Exit');
	}
}