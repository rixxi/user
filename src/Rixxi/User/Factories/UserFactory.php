<?php

namespace Rixxi\User\Factories;

use Kdyby;
use Nette;
use Rixxi;
use Rixxi\User\Entities\User;
use Rixxi\User\Entities\Role;


class UserFactory extends Nette\Object
{

	/** @var Kdyby\Doctrine\EntityDao */
	private $repository;

	/** @var Kdyby\Doctrine\EntityDao */
	private $rolesRepository;

	/** @var Rixxi\User\Security\IPasswordStrategy */
	private $passwordStrategy;


	public function __construct(Kdyby\Doctrine\EntityDao $repository, Rixxi\User\Security\IPasswordStrategy $passwordStrategy)
	{
		$this->repository = $repository;
		$this->rolesRepository = $repository->related('roles');
		$this->passwordStrategy = $passwordStrategy;
	}


	/**
	 * @param string $name
	 * @param string $email
	 * @param string $password
	 * @param Role[]|string[] $roles
	 */
	public function create($name, $email, $password, $roles = [])
	{
		$user = new User;
		$user->name = $name;
		$user->email = $email;
		$this->passwordStrategy->setPassword($user, $password);

		$roles = (array) $roles;
		foreach ($roles as $role) {
			if (is_string($role)) {
				if (!$role = $this->rolesRepository->findBy([ 'name' => $role ])) {
					throw new \RuntimeException("Unknown role '$role'.");
				}

			} elseif (!$role instanceof Role) {
				throw new \UnexpectedValueException("Role must be string or instance of Rixxi\\User\\Entities\\Role");
			}

			$user->roles->add($role);
		}

		$this->repository->save($user);

		return $user;
	}

}
