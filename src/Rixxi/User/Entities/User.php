<?php

namespace Rixxi\User\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby;
use Rixxi;

/**
 * @property $name
 * @property $email
 * @property $password
 * @property $roles
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="unique_name", columns={"name"}), @ORM\UniqueConstraint(name="unique_email", columns={"email"})})
 */
class User extends Kdyby\Doctrine\Entities\IdentifiedEntity implements Rixxi\User\IUser
{

	/**
	 * @ORM\Column(type="string",columnDefinition="VARCHAR(255) COLLATE 'utf8_general_ci' NOT NULL")
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(type="string",columnDefinition="VARCHAR(254) COLLATE 'utf8_general_ci' NOT NULL")
	 * @var string
	 */
	private $email;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $password;

	/**
	 * @ORM\ManyToMany(targetEntity="Role")
	 * @var string
	 */
	private $roles;


	public function getPassword()
	{
		return $this->password;
	}


	public function setPassword($password)
	{
		$this->password = $password;
	}

}