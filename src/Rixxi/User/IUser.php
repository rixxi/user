<?php

namespace Rixxi\User;


interface IUser
{

	/**
	 * Get users id
	 * @return int|string
	 */
	function getId();

	/**
	 * Get users password
	 * @return string
	 */
	function getPassword();

	/**
	 * Sets users password
	 * @return void
	 */
	function setPassword($password);

}
