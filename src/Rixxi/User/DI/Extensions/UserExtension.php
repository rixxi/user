<?php

namespace Rixxi\User\DI\Extensions;

use Kdyby;
use Nette;
use Nette\Utils\Validators;


class UserExtension extends Nette\DI\CompilerExtension implements Kdyby\Doctrine\DI\IEntityProvider
{

	private $defaults = array(
		'signIn' => array(
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
			'Rixxi\\User\\Entities' => __DIR__ . '/../../Entities',
		);
	}


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults);

		Validators::assertField($config, 'signIn', 'array');
		Validators::assertField($config['signIn'], 'redirect', 'string');
		Validators::assertField($config['signIn'], 'expiration', 'string');
		Validators::assertField($config['signIn'], 'backlink', 'string');

		$container->addDefinition($this->prefix('signInFormFactory'))
			->setClass('Rixxi\User\Application\UI\SignInFormFactory')
			->addSetup('setRedirectAfter', $config['signIn']['redirect'])
			->addSetup('setUserExpiration', $config['signIn']['expiration'])
			->addSetup('setPresenterBacklinkParameter', $config['signIn']['backlink']);

		$container->addDefinition($this->prefix('signInForm'))
			->setFactory($this->prefix('@signInFormFactory'));

		$container->addDefinition($this->prefix('passwordStrategy'))
			->setClass('Rixxi\User\Security\CryptSha512PasswordStrategy');

		$container->addDefinition($this->prefix('authenticator'))
			->setClass('Rixxi\User\Security\Authenticator');

		$container->addDefinition($this->prefix('model'))
			->setClass('Rixxi\User\Model', array('@doctrine.dao(Rixxi\User\Entities\User)'));

		return $config;
	}

}