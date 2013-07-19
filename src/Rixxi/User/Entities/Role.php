<?php

namespace Rixxi\User\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby;


/**
 * @property $name
 *
 * @Table(uniqueConstraints={@UniqueConstraint(name="unique_name", columns={"name"}))})
 */
class Role extends Kdyby\Doctrine\Entities\IdentifiedEntity
{

	/**
	 * @ORM\Column(type="string",columnDefinition="VARCHAR(255) COLLATE 'utf8_general_ci' NOT NULL")
	 * @var string
	 */
	private $name;

}