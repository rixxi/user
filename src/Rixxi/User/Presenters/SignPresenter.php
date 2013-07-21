<?php

namespace Rixxi\User\Presenters;

use Nette;
use Rixxi;


class SignPresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @inject
	 * @var Rixxi\User\Application\UI\SignInFormFactory
	 */
	public $signInFormFactory;


	public function createComponentSignInForm()
	{
		return $this->signInFormFactory->createSignInForm();
	}


	public function actionOut()
	{
		$this->getUser()->logout();
	}

}
