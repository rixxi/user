<?php

namespace Rixxi\User\DI;

use Kdyby;
use Nette;
use Nette\Utils\Validators;
use Rixxi;


class UserExtension extends Nette\DI\CompilerExtension implements Kdyby\Doctrine\DI\IEntityProvider
{

	use Rixxi\Modular\DI\CompilerExtensionSugar;


	private $defaults = array(
		'signIn' => array(
			'redirect' => NULL,
			'expiration' => NULL,
			'backlink' => NULL,
		),
		'signOut' => array(
			'redirect' => NULL,
		),
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


	public function loadConfiguration()
	{
		$doctrine = $this->getCompilerExtension('Kdyby\Doctrine\DI\OrmExtension');

		$container = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults);

		$this->loadConfig('console');

		Validators::assertField($config, 'signIn', 'array');
		Validators::assertField($config['signIn'], 'redirect', 'string|null');
		Validators::assertField($config['signIn'], 'expiration', 'string|int|null');
		Validators::assertField($config['signIn'], 'backlink', 'string|null');

		$container->addDefinition($this->prefix('signInFormFactory'))
			->setClass('Rixxi\User\Application\UI\SignInFormFactory');

		$container->addDefinition($this->prefix('passwordStrategy'))
			->setClass('Rixxi\User\Security\CryptSha512PasswordStrategy');

		if (NULL === $container->getByType('Nette\Security\IAuthenticator')) {
			$container->addDefinition($this->prefix('authenticator'))
				->setClass('Rixxi\User\Security\Authenticator');
		}

		$container->addDefinition($this->prefix('repository'))
			->setFactory($doctrine->prefix('@dao'), array('Rixxi\User\Entities\User'));

		$container->addDefinition($this->prefix('model'))
			->setClass('Rixxi\User\Models\DoctrineModel', array($this->prefix('@repository')));

		$container->addDefinition($this->prefix('userFactory'))
			->setClass('Rixxi\User\Factories\UserFactory', array($this->prefix('@repository')));

		$container->addDefinition($this->prefix('listener'))
			->setClass('Rixxi\User\Listener', array(1 => $this->prefix('@repository')))
			->addTag(Kdyby\Events\DI\EventsExtension::TAG_SUBSCRIBER)
			->setAutowired(FALSE);

		$container->addDefinition($this->prefix('signedUser'))
			->setClass('Rixxi\User\Models\SignedUser', array(1 => $this->prefix('@repository')));

		$container->addDefinition($this->prefix('user'))
			->setClass('Rixxi\User\User', array(3 => $config['signIn']['expiration'], $config['signIn']['backlink']));

		foreach (array('signIn', 'signOut') as $event) {
			if (($redirect = $config[$event]['redirect']) !== NULL) {
				$class = 'Rixxi\Bridges\UserKdybyEvents\RedirectOn' . ucfirst($event) . 'Subscriber';
				if (!class_exists($class)) {
					throw new \RuntimeException("Please install rixxi/redirector package to enable redirect functionality.");
				}
				$container->addDefinition($this->prefix('userOn' . ucfirst($event) . 'Redirector'))
					->setClass($class, array(1 => $redirect))
					->addTag(Kdyby\Events\DI\EventsExtension::TAG_SUBSCRIBER);
			}
		}

		return $config;
	}

}
