<?php

namespace Rixxi\User;


interface IModel
{

	/**
	 * Retrieves user by username or email
	 * @param string $nameOrEmail
	 * @return IUser|NULL
	 */
	function getByNameOrEmail($nameOrEmail);

	/**
	 * Return all roles associated with user
	 * @param IUser $user
	 * @return string[]
	 */
	function getRoles(IUser $user);

}
