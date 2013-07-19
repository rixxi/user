<?php

namespace Rixxi\User\Queries;

use Kdyby;


class FindUserByUsernameOrEmailQuery extends Kdyby\Doctrine\QueryObject
{
	/** @var string */
	private $usernameOrEmail;


	/**
	 * @param string $usernameOrEmail
	 */
	public function __construct($usernameOrEmail)
	{
		$this->usernameOrEmail = $usernameOrEmail;
	}


	/** @inherit */
	protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
	{
		return $repository->select()
			->where('username = :usernameOrEmail OR email = :usernameOrEmail')
			->setParameter('usernameOrEmail', $this->usernameOrEmail);
	}

}