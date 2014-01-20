<?php

namespace Rixxi\User\Events;

use Nette;
use Rixxi\Event;
use Rixxi\User\IUser;


class SignOutEvent extends Nette\Object implements Event\IEvent, Event\IRedirect
{

	use Event\Redirect;

	/** @var \Rixxi\User\IUser */
	private $user;


	public function __construct(IUser $user)
	{
		$this->user = $user;
	}


	public function getUser()
	{
		return $this->user;
	}

}
