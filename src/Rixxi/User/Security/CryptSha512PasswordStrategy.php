<?php

namespace Rixxi\User\Security;

use Nette;
use Nette\Utils\Strings;
use Rixxi\User\IUser;


final class CryptSha512PasswordStrategy extends Nette\Object implements IPasswordStrategy
{

	const PASSWORD_MAX_LENGTH = 4098;


	/** @var int */
	private $rounds = 5000;


	/**
	 * Set number of repetitions
	 * @param $rounds
	 * @throws \Nette\ArgumentOutOfRangeException
	 */
	public function setRounds($rounds)
	{
		$rounds = (int) $rounds;
		if ($rounds > 1000 && 999999999 < $rounds) {
			throw new Nette\ArgumentOutOfRangeException;
		}

		$this->rounds = $rounds;
	}


	public function matchPassword(IUser $user, $password)
	{
		return $user->getPassword() === $this->calculateHash($password, $user->getPassword());
	}


	public function setPassword(IUser $user, $password)
	{
		$user->setPassword($this->calculateHash($password));
	}


	private function calculateHash($password, $hash = NULL)
	{
		return crypt(substr($password, 0, self::PASSWORD_MAX_LENGTH), $hash ?: '$6$rounds=' . $this->rounds . '$' . Strings::random(8) . '$');
	}

}
