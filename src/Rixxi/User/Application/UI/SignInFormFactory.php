<?php

namespace Rixxi\User\Application\UI;

use Nette;
use Rixxi\FormFactory\IFormFactory;


class SignInFormFactory extends Nette\Object
{

	/** @var callback[] */
	public $onCreate = array();

	/** @var IFormFactory */
	private $formFactory;

	/** @var Nette\Security\User */
	private $user;


	public function __construct(IFormFactory $formFactory, Nette\Security\User $user)
	{
		$this->formFactory = $formFactory;
		$this->user = $user;
	}


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	public function create()
	{
		$form = $this->formFactory->create();

		$form->addText('username', 'Username:')
			->setRequired();

		$form->addPassword('password', 'Password:')
			->setRequired();

		$form->addSubmit('send', 'Sign in');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->onSuccess;

		$this->onCreate($form);

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
