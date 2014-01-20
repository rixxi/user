<?php

namespace Rixxi\User;

use Nette;
use Nette\Application\Application;
use Nette\Security\User as SecurityUser;
use Rixxi\Event\Redirector;
use Rixxi\Event\Helper as EventHelper;
use Rixxi\User\Events\SignInEvent;
use Rixxi\User\Events\SignOutEvent;
use Rixxi\User\IUser;


class User extends Nette\Object
{

	/** @var callback[] */
	public $onSignIn = array();

	/** @var callback[] */
	public $onSignOut = array();

	/** @var \Rixxi\Event\Redirector */
	private $redirector;

	/** @var \Nette\Application\Application */
	private $application;

	/** @var \Nette\Security\User */
	private $security;

	/** @var int|string|null */
	private $expiration;

	/** @var string|null */
	private $backlink;


	public function __construct(Redirector $redirector, Application $application, SecurityUser $security, $expiration = NULL, $backlink = NULL)
	{
		$this->redirector = $redirector;
		$this->application = $application;
		$this->security = $security;
		$this->expiration = $expiration;
		$this->backlink = $backlink;
	}


	public function signIn(IUser $user)
	{
		$this->onSignIn($event = new SignInEvent($user));

		if ($this->expiration !== NULL) {
			$this->security->setExpiration($this->expiration);
		}

		if ($this->backlink !== NULL) {
			$presenter = $this->application->getPresenter();
			if (isset($presenter->{$this->backlink})) {
				$presenter->restoreRequest($presenter->{$this->backlink});
			}
		}

		$this->redirector->handle($event);
	}


	public function signOut(IUser $user)
	{
		$this->onSignOut($event = new SignOutEvent($user));
		$this->redirector->handle($event);
	}

}
