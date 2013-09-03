<?php

namespace Rixxi\User\DI;

use Kdyby;
use Nette;
use Nette\Utils\Validators;
use Nette\DI\Statement;
use Rixxi;


class UserExtension extends Nette\DI\CompilerExtension implements Kdyby\Doctrine\DI\IEntityProvider, Rixxi\Modular\DI\IPresenterMappingProvider
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
	public function getEntityMappings()
	{
		return array(
			'Rixxi\\User\\Entities' => __DIR__ . '/../Entities',
		);
	}


	public function getPresenterMapping()
	{
		return array(
			'User' => 'Rixxi\User\Presenters\*Presenter',
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
			->addSetup('setBacklinkParameter', $config['signIn']['backlink']);

		$container->addDefinition($this->prefix('signInForm'))
			->setFactory($this->prefix('@signInFormFactory'));

		$container->addDefinition($this->prefix('passwordStrategy'))
			->setClass('Rixxi\User\Security\CryptSha512PasswordStrategy');

		if (NULL === $container->getByType('Nette\Security\IAuthenticator')) {
			$container->addDefinition($this->prefix('authenticator'))
				->setClass('Rixxi\User\Security\Authenticator');
		}

		$container->addDefinition($this->prefix('repository'))
			->setFactory('@doctrine.dao', array('Rixxi\User\Entities\User'));

		$container->addDefinition($this->prefix('model'))
			->setClass('Rixxi\User\Models\DoctrineModel', array($this->prefix('@repository')));

		$container->addDefinition($this->prefix('userFactory'))
		->setClass('Rixxi\User\Factories\UserFactory', array($this->prefix('@repository')));

		return $config;
	}

}
