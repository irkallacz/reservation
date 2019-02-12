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
use Latte\Engine;
use Nette\Mail\IMailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class VisitRequestCommand extends Command
{
	/** @var Orm */
	private $orm;

	/** @var IMailer */
	private $mailer;

	/**
	 * VisitRequestCommand constructor.
	 * @param Orm $orm
	 */
	public function __construct(Orm $orm, IMailer $mailer) {
		parent::__construct();
		$this->orm = $orm;
		$this->mailer = $mailer;
	}

	/**
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('requests:dispatch');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$requests = $this->orm->visitRequests->findBy(['dateEnd<=' => new \DateTimeImmutable()]);
		$output->writeln('Deleting '.$requests->count().' old requests');
		foreach ($requests as $request)
		{
			$person = $request->person;
			$this->orm->visitRequests->remove($person->visitRequest);
			$this->orm->persistAndFlush($person);

		}
		$output->writeln('Done');

		/** @var Orm\Visit[] $visits*/
		$visits = $this->orm->visits->findBy(['open' => TRUE, 'dateStart>=' => new \DateTimeImmutable('+24 hour')])
			->orderBy('dateStart')
			->fetchPairs('id');

		$output->writeln('Have '.count($visits).' open visits');

		/** @var Orm\VisitRequest[] $requests */
		$requests = $this->orm->visitRequests->findBy(['active' => TRUE])
			->orderBy('dateAdd')
			->fetchPairs('id');

		$output->writeln('Have '.count($requests).' active requests');

		$latte = new \Latte\Engine;

		if ((count($visits))and(count($requests))) {
			foreach ($visits as $visit)
				foreach ($requests as $request)
					if (($visit->canSatisfyRequest($request))and(array_key_exists($request->id, $requests))) {
						$output->writeln('Found visit ID' . $visit->id . ' witch satisfy request ID' . $request->id);

						$person = $request->person;

						$mail = Email::newMessage();
						$mail->addTo($person->mail, $person->fullName);
						$mail->setSubject('Uvolněné místo na vyšetření');
						$mail->setBody($latte->renderToString(__DIR__ . '/../app/presenters/templates/Email/request.latte', ['date' => $visit->dateStart]));

						$this->mailer->send($mail);

						$request->active = FALSE;
						$this->orm->persistAndFlush($request);
						$output->writeln('E-mail notification send, request deactivated');

						unset($requests[$request->id]);
					}
		}else
		{
			$output->writeln('Have nothing to do, exiting');
		}

		$output->writeln('Exit');
	}
}