<?php

namespace Rixxi\User\Application\UI;

use Nette;
use Rixxi;


class SignInFormFactory extends Nette\Object
{

	/** @var callback[] */
	public $onBeforeFormCreated = array();

	/** @var Rixxi\IFormFactory */
	private $formFactory;

	/** @var Nette\Security\User */
	private $user;

	private $redirectAfter;

	/** @var string|NULL */
	private $backlinkParameter = NULL;

	/** @var string|int|DateTime */
	private $userExpiration = 0;


	public function __construct(Rixxi\IFormFactory $formFactory, Nette\Security\User $user)
	{
		$this->formFactory = $formFactory;
		$this->user = $user;
	}


	/**
	 * Link to redirect to on login
	 * @param $redirectAfter
	 */
	public function setRedirectAfter($redirectAfter)
	{
		$this->redirectAfter = $redirectAfter;
	}


	/**
	 * Sets backlink parameter which stores key for restoring requests
	 *
	 * @var string|NULL
	 */
	public function setBacklinkParameter($backlinkParameter)
	{
		$this->backlinkParameter = $backlinkParameter;
	}


	/**
	 * Sets session expiration on login
	 * @var string|int|DateTime
	 */
	public function setUserExpiration($userExpiration)
	{
		$this->userExpiration = $userExpiration;
	}


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	public function createSignInForm()
	{
		$form = $this->formFactory->createForm();

		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addSubmit('send', 'Sign in');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->onSuccess;

		$this->onBeforeFormCreated($form);

		return $form;
	}


	public function onSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();

		$this->user->setExpiration($this->userExpiration);

		try {
			$this->user->login($values->username, $values->password);

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());

			return;
		}

		if (NULL !== $this->backlinkParameter && isset($form->getPresenter()->{$this->backlinkParameter})) {
			$form->getPresenter()->restoreRequest($form->getPresenter()->{$this->backlinkParameter});
		}
		$form->getPresenter()->redirect($this->redirectAfter);
	}

}
