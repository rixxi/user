<?php

namespace Rixxi\User\Security;

use Rixxi\User\IUser;


interface IPasswordStrategy
{

	/**
	 * Checks is users password matches provided password
	 *
	 * @param IUser $user
	 * @param $password
	 * @return bool
	 */
	function matchPassword(IUser $user, $password);

	/**
	 * Sets new password
	 * @param IUser $user
	 * @param $password
	 * @return void
	 */
	function setPassword(IUser $user, $password);

}
