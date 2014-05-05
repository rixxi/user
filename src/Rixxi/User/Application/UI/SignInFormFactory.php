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


	public function __construct(Rixxi\IFormFactory $formFactory, Nette\Security\User $user)
	{
		$this->formFactory = $formFactory;
		$this->user = $user;
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

		try {
			$this->user->login($values->username, $values->password);

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());

			return;
		}
	}

}
