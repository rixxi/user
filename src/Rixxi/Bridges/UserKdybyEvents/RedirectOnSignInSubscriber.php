<?php

namespace Rixxi\Bridges\UserKdybyEvents;

use Rixxi;


class RedirectOnSignInSubscriber extends Rixxi\Bridges\RedirectorKdybyEvents\RedirectSubscriber
{

	protected function getEvents()
	{
		return array('Rixxi\User\User::onSignIn');
	}

}
