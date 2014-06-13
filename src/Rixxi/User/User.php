<?php

namespace Rixxi\User;

use Nette;
use Nette\Application\Application;
use Nette\Security\User as SecurityUser;


class User extends Nette\Object
{

	/** @var callback[] */
	public $onSignIn = array();

	/** @var callback[] */
	public $onSignOut = array();

	/** @var \Nette\Application\Application */
	private $application;

	/** @var \Nette\Security\User */
	private $security;

	/** @var int|string|null */
	private $expiration;

	/** @var string|null */
	private $backlink;


	public function __construct(Application $application, SecurityUser $security, $expiration = NULL, $backlink = NULL)
	{
		$this->application = $application;
		$this->security = $security;
		$this->expiration = $expiration;
		$this->backlink = $backlink;
	}


	public function signIn(IUser $user)
	{
		$this->onSignIn($user);

		if ($this->expiration !== NULL) {
			$this->security->setExpiration($this->expiration);
		}

		if ($this->backlink !== NULL) {
			$presenter = $this->application->getPresenter();
			if (isset($presenter->{$this->backlink})) {
				$presenter->restoreRequest($presenter->{$this->backlink});
			}
		}
	}


	public function signOut(IUser $user)
	{
		$this->onSignOut($user);
	}

}
