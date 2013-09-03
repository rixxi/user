<?php

namespace Rixxi\User\Commands;


use Kdyby;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Rixxi;
use Symfony;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tester\Assert;


class CreateCommand extends Symfony\Component\Console\Command\Command
{

	/** @var Rixxi\User\IModel */
	private $factory;

	/** @var Rixxi\User\Factories\UserFactory */
	private $model;


	protected function configure()
	{
		$this->setName('user:create');
		$this->addArgument('name', InputOption::VALUE_REQUIRED);
		$this->addArgument('email', InputOption::VALUE_REQUIRED);
		$this->addOption('password', 'p', InputOption::VALUE_OPTIONAL | InputOption::VALUE_NONE, 'If is not specified will generate and return.');
		$this->addOption('generate-password', 'g', InputOption::VALUE_NONE, 'Generate password.');
		$this->addOption('role', 'r', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY);
		$this->setDescription('Creates user with username and email.');
	}


	protected function initialize(InputInterface $input, OutputInterface $output)
	{

		$context = $this->getHelper('container');
		$this->model = $context->getByType('Rixxi\\User\\IModel');
		$this->factory = $context->getByType('Rixxi\\User\\Factories\\UserFactory');
	}


	protected function interact(InputInterface $input, OutputInterface $output)
	{
		if ($input->hasOption('password') && $input->getOption('password') === NULL) {
			/** @var Symfony\Component\Console\Helper\DialogHelper $dialog */
			$dialog = $this->getHelper('dialog');
			$password = $dialog->askHiddenResponseAndValidate($output, 'Please enter password: ', function ($value) {
				$value = trim($value);
				if ($value === '') {
					throw new \Exception('Password can not be empty');
				}
				return $value;
			});
			$input->setOption('password', $password);
		}
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if ($input->hasOption('password') && $input->getOption('password') === NULL) {
			$output->writeln('<error>Please enable interactive mode to set password.</error>');
			return 1;
		}

		if ($input->hasOption('password') && $input->getOption('generate-password')) {
			$output->writeln('<error>Cannot set and generate password at once.</error>');
			return 1;
		}

		if (!$input->hasOption('password') && !$input->getOption('generate-password')) {
			$output->writeln('<error>Cannot create user without password.</error>');
			return 1;
		}

		$name = $input->getArgument('name');
		if ($this->model->getByNameOrEmail($name)) {
			$output->writeln('<error>User with same name already exists.</error>');
			return 1;
		}

		$email = $input->getArgument('email');
		if (!Validators::is($email, 'email')) {
			$output->writeln('<error>Invalid email</error>');
			return 1;
		}

		if ($this->model->getByNameOrEmail($email)) {
			$output->writeln('<error>User with same email already exists.</error>');
			return 1;
		}

		$printPassword = FALSE;
		if ($input->getOption('password') !== NULL) {
			$password = $input->getOption('password');

		} elseif ($this->getOption('generate-password')) {
			$password = Strings::random();
			$printPassword = TRUE;
		}

		$roles = $input->getOption('role');

		$this->factory->create($name, $email, $password, $roles);

		if ($printPassword) {
			$verbosity = $output->getVerbosity();
			$output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
			$output->write('<info>');
			$output->write($password, OutputInterface::OUTPUT_RAW);
			$output->writeln('</info>');
			$output->setVerbosity($verbosity);
		}
	}

}
