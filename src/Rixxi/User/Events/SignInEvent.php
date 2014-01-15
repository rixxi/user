<?php

namespace Rixxi\User\Events;

use Nette;
use Rixxi\Event;


class SignInEvent extends Nette\Object implements Event\IEvent, Event\IRedirect
{

	use Event\Redirect;

}
