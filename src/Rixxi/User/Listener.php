<?php

namespace Rixxi\User;

use Doctrine\ORM\EntityRepository;
use Kdyby;
use Nette;
use Nette\Security\User as Security;


class Listener extends Nette\Object implements Kdyby\Events\Subscriber
{

	/** @var \Rixxi\User\User */
	private $user;

	/** @var \Doctrine\ORM\EntityRepository */
	private $repository;


	public function __construct(User $user, EntityRepository $repository)
	{
		$this->user = $user;
		$this->repository = $repository;
	}


	public function onLoggedIn(Security $security)
	{
		if ($user = $this->repository->find($security->getIdentity()->getId())) {
			$this->user->signIn($user);
		}
	}


	public function onLoggedOut(Security $security)
	{
		// BUG: Nette\Security\User 2.1 fires onLoggedOut before clearing storage
		if ($user = $this->repository->find($security->getIdentity()->getId())) {
			$security->getStorage()->setAuthenticated(FALSE);
			$this->user->signOut($user);
		}
	}


	public function getSubscribedEvents()
	{
		return array(
			'Nette\Security\User::onLoggedIn',
			'Nette\Security\User::onLoggedOut',
		);
	}

}
