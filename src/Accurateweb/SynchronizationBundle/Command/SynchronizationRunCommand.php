<?php

namespace Accurateweb\SynchronizationBundle\Command;

use Accurateweb\SynchronizationBundle\Model\Configuration\SynchronizationServiceConfiguration;
use Accurateweb\SynchronizationBundle\Model\SynchronizationMode;
use Accurateweb\SynchronizationBundle\Model\SynchronizationResult;
use Accurateweb\SynchronizationBundle\Model\SynchronizationScenario;
use Accurateweb\SynchronizationBundle\Model\SynchronizationService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SynchronizationRunCommand
 * @package Accurateweb\SynchronizationBundle\Command
 */
class SynchronizationRunCommand extends ContainerAwareCommand
{

  /** @var $logger Logger */
  private $logger;
  private $rootDir;
  private $dispatcher;
  private $databaseManager;

  function setVars()
  {
    $this->logger = $this->getContainer()->get('logger');
    $this->dispatcher = $this->getContainer()->get('event_dispatcher');
    $this->rootDir =  $this->getContainer()->get('kernel')->getRootDir();

  }

  function configure()
  {
    $desc = 'The [catalog:synchronize|INFO] task updates local database
using remote datasource.

Synchronized table depends on chosen subject

Example: php symfony synchronization:run --datasource=local --filename=/path catalog

Call it with:

  [php symfony synchronization:run|INFO]';

    $this
      ->setName('synchronization:run')
      ->addArgument('subject', InputOption::VALUE_OPTIONAL,
        'Synchronization subject or scenario')
      ->addOption('application', null, InputOption::VALUE_REQUIRED,
        'The application name')
      ->addOption('connection', null, InputOption::VALUE_REQUIRED,
        'The connection name', 'doctrine')
      ->addOption('datasource', null, InputOption::VALUE_REQUIRED, 'Datasource name')
      ->addOption('filename', null, InputOption::VALUE_OPTIONAL,
        'Filename to parse. If scenario is provided, this parameter is ignored')
      ->addOption('mode', null, InputOption::VALUE_OPTIONAL,
        'Synchronization mode. Must be "full" or "incremental"', SynchronizationMode::FULL)
      ->setDescription($desc);

  }

  function execute(InputInterface $input, OutputInterface $output)
  {
    $this->setVars();
    $exitCode = 0;

    $subject = $input->getArgument('subject');

    if (isset($subject[0]))
    {
      $subject = $subject[0];
    } else
    {
      throw new InvalidConfigurationException('Укажите сценарий');
    }

    $configuration = $this->getServiceConfiguration($input->getOptions());
    $scenario = $configuration->getScenario($subject);

    $administratorEmail = $this->getContainer()->getParameter('operator_email');

    if (is_null($scenario))
    {
      $scenario = new SynchronizationScenario($this->dispatcher);
      $scenario->addSubject($input->getArgument('subject'));
    }

    $scenario->preExecute();

    $service = new SynchronizationService($configuration, $this->dispatcher);
    foreach ($scenario as $subject)
    {
      $subject = is_array($subject) ? $subject[0] :$subject;

      $this->logger->addInfo(sprintf('synchronizing subject: %s...', $subject));

      try
      {
        $service->pull($subject, $input->getOptions());
        $this->logger->addInfo("SynchronizationResult: " . SynchronizationResult::OK);
      } catch (\Exception $e)
      {
        $this->logger->addInfo("SynchronizationResult: " . SynchronizationResult::INTERNAL_SERVER_ERROR);
        $this->logger->addError($e->getMessage());
        $output->writeln($e->getMessage());
        $exitCode = 1;
      }

      $this->logger->addInfo("SynchronizationFinishedAt: " . date('d-m-Y H:i:s'));

      if ($administratorEmail && $exitCode !== 1)
      {
        try
        {
          $email = $this->getContainer()->get('aw_email_templating.template.factory')->createMessage(
            'synchronization_error_admin',
            array($this->getContainer()->getParameter('mailer_from') => $this->getContainer()->getParameter('mailer_sender_name')),
            array($administratorEmail => ''),
            array(
              'time' => date('d.m.Y H:i:s'),
              'subject' => $subject,
              'code' => SynchronizationResult::INTERNAL_SERVER_ERROR,
              'message' => $e->getMessage()
            ));

          $this->getContainer()->get('mailer')->send($email);
        } catch (\Exception $mailSendException)
        {
          $this->logger->error(sprintf('Unable to send email for checkout event: "%s"',
                                                                          $mailSendException->getMessage()));
        }
      }
    }

    $this->logger->addInfo('Performing post-execute operations...');

    $scenario->postExecute();

    $this->logger->addInfo('complete');
    $output->write('Synchronization complete.');

    return $exitCode;
  }

  /**
   *
   * @param mixed $connection
   * @return SynchronizationServiceConfiguration
   */
  protected function getServiceConfiguration($connection)
  {
    $configuration = new SynchronizationServiceConfiguration($this->dispatcher, $this->rootDir);
    $configuration->setDbConnection($this->getConnection());
    $configuration->load($this->rootDir . "/config/parser.yml");

    return $configuration;
  }

  protected function getConnection()
  {
    return $this->getContainer()->get('doctrine.orm.default_entity_manager')->getConnection();
  }

}