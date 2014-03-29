<?php

namespace Rixxi\User\Security;

use Nette;
use Nette\Utils\Strings;
use Rixxi\User\IUser;


final class CryptSha512PasswordStrategy extends Nette\Object implements IPasswordStrategy
{

	const PASSWORD_MAX_LENGTH = 4098;

	const ROUNDS_MIN = 1000;

	const ROUNDS_MAX = 999999999;

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
		if ($rounds < self::ROUNDS_MIN || self::ROUNDS_MAX < $rounds) {
			throw new Nette\ArgumentOutOfRangeException("Rounds must be between " . self::ROUNDS_MIN . " and " . self::ROUNDS_MAX . ", value $rounds given.");
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
