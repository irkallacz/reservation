<?php
/**
 * Created by PhpStorm.
 * User: Jakub
 * Date: 07.03.2018
 * Time: 10:41
 */

namespace App\Model;


use Nette\Mail\Message;
use Nette\StaticClass;

final class Email
{
	use StaticClass;
	/**
	 * @return Message
	 */
	public static function newMessage(): Message
	{
		$message = new Message();
		$message->setFrom('Centrum sportovní medicíny <sportovnimedicina@tul.cz>');

		return $message;
	}
}