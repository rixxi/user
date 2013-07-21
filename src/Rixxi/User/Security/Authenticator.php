<?php

namespace Rixxi\User\Security;

use Nette;
use Nette\DateTime;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Rixxi;


class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var Rixxi\User\IModel */
	private $model;

	/** @var Rixxi\User\Security\IPasswordStrategy */
	private $passwordStrategy;


	public function __construct(Rixxi\User\IModel $model, IPasswordStrategy $passwordStrategy)
	{
		$this->model = $model;
		$this->passwordStrategy = $passwordStrategy;
	}


	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$user = $this->model->getByUsernameOrEmail($username);

		if (!$user) {
			throw new AuthenticationException('User not found.', self::IDENTITY_NOT_FOUND);
		}

		if (!$this->passwordStrategy->matchPassword($user, $password)) {
			throw new AuthenticationException('Invalid password.', self::INVALID_CREDENTIAL);
		}

		unset($user->password);
		return new Identity($user->getId(), $this->model->getRoles($user), $user->toArray());
	}

}
