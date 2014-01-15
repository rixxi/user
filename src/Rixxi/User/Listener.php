<?php

namespace Rixxi\User;

use Kdyby;
use Nette;
use Nette\Security\User as SecurityUser;


class Listener extends Nette\Object implements Kdyby\Events\Subscriber
{

	/** @var \Rixxi\User\User */
	private $user;


	public function __construct(User $user)
	{
		$this->user = $user;
	}


	public function onLoggedIn(SecurityUser $user)
	{
		$this->user->signIn();
	}


	public function getSubscribedEvents()
	{
		return array(
			'Nette\Security\User::onLoggedIn',
		);
	}

}
