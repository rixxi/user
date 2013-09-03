<?php

namespace Rixxi\User\Models;

use Kdyby;
use Nette;
use Rixxi\User\Queries\FindUserByNameOrEmailQuery;
use Rixxi;


class DoctrineModel extends Nette\Object implements Rixxi\User\IModel
{

	/** @var \Kdyby\Doctrine\EntityDao */
	private $repository;


	public function __construct(Kdyby\Doctrine\EntityDao $repository)
	{
		$this->repository = $repository;
	}


	public function getByNameOrEmail($nameOrEmail)
	{
		return $this->repository->fetchOne(new FindUserByNameOrEmailQuery($nameOrEmail));
	}


	public function getRoles(Rixxi\User\IUser $user)
	{
		$roles = array('user');
		foreach ($user->getRoles() as $role) {
			$roles[] = $role->name;
		}
		return array_unique($roles);
	}

}
