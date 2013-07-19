<?php

namespace Rixxi\User;

use Kdyby;
use Nette;


class Model extends Nette\Object implements IModel
{
	/** @var \Kdyby\Doctrine\EntityDao */
	private $repository;


	public function __construct(Kdyby\Doctrine\EntityDao $repository)
	{
		$this->repository = $repository;
	}


	public function getByUsernameOrEmail($usernameOrEmail)
	{
		return $this->repository->fetchOne(new Queries\FindUserByUsernameOrEmailQuery($usernameOrEmail));
	}


	public function getRoles(IUser $user)
	{
		$roles = array('user');
		foreach ($user->getRoles() as $role) {
			$roles[] = $role->name;
		}
		return array_unique($roles);
	}
}