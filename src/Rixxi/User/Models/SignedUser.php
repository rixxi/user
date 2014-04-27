<?php

namespace Rixxi\User\Models;

use Kdyby;
use Nette;
use Rixxi\User\Queries\FindUserByNameOrEmailQuery;
use Rixxi;
use Nette\Security\User as SecurityUser;


class SignedUser extends Nette\Object
{

	/** @var \Nette\Security\User */
	private $securityUser;

	/** @var \Kdyby\Doctrine\EntityDao */
	private $repository;


	public function __construct(SecurityUser $securityUser, Kdyby\Doctrine\EntityDao $repository)
	{
		$this->securityUser = $securityUser;
		$this->repository = $repository;
	}


	/** @return \Rixxi\User\IUser|null */
	public function getSignedUser()
	{
		if ($this->securityUser->isLoggedIn()) {
			return $this->repository->find($this->securityUser->getIdentity()->getId());
		}
	}

}
