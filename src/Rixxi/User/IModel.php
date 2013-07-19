<?php

namespace Rixxi\User;


interface IModel
{

	/**
	 * Retrieves user by username or email
	 * @param string $usernameOrEmail
	 * @return IUser|NULL
	 */
	function getByUsernameOrEmail($usernameOrEmail);

	/**
	 * Return all roles associated with user
	 * @param IUser $user
	 * @return string[]
	 */
	function getRoles(IUser $user);

}
