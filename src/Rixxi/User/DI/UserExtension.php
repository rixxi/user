<?php

namespace Rixxi\User\DI;

use Kdyby;
use Nette;
use Nette\Utils\Validators;


class UserExtension extends Nette\DI\CompilerExtension implements Kdyby\Doctrine\DI\IEntityProvider
{

	private $defaults = array(
		'user' => array(
			'redirect' => ':Homepage:Default:',
			'expiration' => '0',
			'backlink' => 'backlink',
		)
	);

	/**
	 * Returns associative array of Namespace => mapping definition
	 *
	 * @return array
	 */
	function getEntityMappings()
	{
		return array(
			'Rixxi\\User\\Entities' => __DIR__ . '/Rixxi/User/Entities',
		);
	}


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults);

		Validators::assertField($config, 'redirect', 'string');
		Validators::assertField($config, 'expiration', 'string');
		Validators::assertField($config, 'backlink', 'string');

		$container->addDefinition($this->prefix('signInFormFactory'), 'Rixxi\User\Application\UI\SignInFormFactory')
			->addSetup('setRedirectAfter', $config['redirect'])
			->addSetup('setUserExpiration', $config['expiration'])
			->addSetup('setPresenterBacklinkParameter', $config['backlink']);

		$container->addDefinition($this->prefix('passwordStrategy'), 'Rixxi\User\Security\CryptSha512PasswordStrategy');

		$container->addDefinition($this->prefix('authenticator'))
			->setClass('Rixxi\User\Security\Authenticator', array($config['users']));

		$container->addDefinition($this->prefix('model'))
			->setClass('Rixxi\User\Model', array('@doctrine.dao(Rixxi\User\Entities\User)'));

		return $config;
	}

}