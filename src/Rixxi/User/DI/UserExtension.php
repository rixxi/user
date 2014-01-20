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
			->setClass('Rixxi\User\Listener')
			->addTag(Kdyby\Events\DI\EventsExtension::SUBSCRIBER_TAG)
			->setAutowired(FALSE);

		$user = $container->addDefinition($this->prefix('user'))
			->setClass('Rixxi\User\User', array(3 => $config['signIn']['expiration'], $config['signIn']['backlink']));

		if ($config['signIn']['redirect'] !== NULL) {
			$user->addSetup('?->onSignIn[] = ?', array('@self', new Nette\DI\Statement('Rixxi\Event\Helper::defaultRedirect(?)', array($config['signIn']['redirect']))));
		}
		if ($config['signOut']['redirect'] !== NULL) {
			$user->addSetup('?->onSignOut[] = ?', array('@self', new Nette\DI\Statement('Rixxi\Event\Helper::defaultRedirect(?)', array($config['signOut']['redirect']))));
		}

		return $config;
	}

}
