<?php

namespace Rixxi\User\Queries;

use Kdyby;


class FindUserByUsernameOrEmailQuery extends Kdyby\Doctrine\QueryObject
{
	/** @var string */
	private $nameOrEmail;


	/**
	 * @param string $nameOrEmail
	 */
	public function __construct($nameOrEmail)
	{
		$this->nameOrEmail = $nameOrEmail;
	}


	/** @inherit */
	protected function doCreateQuery(Kdyby\Persistence\Queryable $repository)
	{
		return $repository->select('user')
			->where('name = :nameOrEmail OR email = :nameOrEmail')
			->setParameter('nameOrEmail', $this->nameOrEmail);
	}

}