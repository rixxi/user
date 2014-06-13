<?php

namespace Rixxi\Bridges\UserKdybyEvents;

use Rixxi;


class RedirectOnSignOutSubscriber extends Rixxi\Bridges\RedirectorKdybyEvents\RedirectSubscriber
{

	protected function getEvents()
	{
		return array('Rixxi\User\User::onSignOut');
	}

}
