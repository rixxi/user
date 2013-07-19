<?php

namespace Rixxi\User\Security;

use Nette;
use Nette\Utils\Strings;
use Rixxi\User\IUser;


final class CryptSha512PasswordStrategy extends Nette\Object implements IPasswordStrategy
{

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
		return $user->getPassword() === crypt($password, $user->getPassword());
	}


	public function setPassword(IUser $user, $password)
	{
		$user->setPassword(crypt($password, $this->computeHash()));
	}


	private function computeHash()
	{
		return '$6$rounds=' . $this->rounds . '$' . Strings::random(8) . '$';
	}

}