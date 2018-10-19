<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.06.2017
 * Time: 21:14
 */

namespace Accurateweb\SphinxSearchBundle\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexerCommand extends SphinxSearchCommand
{
  protected function configure()
  {
    $this
      // the name of the command (the part after "bin/console")
      ->setName('sphinx:index')
      // the short description shown while running "php bin/console list"
      ->setDescription('Performs sphinx indexing.')
      // the full command description shown when running the command with
      // the "--help" option
      ->setHelp("Performs sphinx indexing.")
      ->addOption('All', null, InputOption::VALUE_NONE, '')
      ->addOption('rotate', null, InputOption::VALUE_NONE, '')
      ->addArgument('index', InputArgument::OPTIONAL, '');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $sphinxConfigPath = $this->getContainer()->get('accurateweb.sphinxsearch')->getSphinxConfigFilePath();

    $options = array(
      'config' => sprintf('"%s"', $sphinxConfigPath)
    );
    if ($input->getOption('All'))
    {
      $options['All'] = true;
    }
    if ($input->getOption('rotate'))
    {
      $options['rotate'] = true;
    }

    $arguments = array();
    if ($input->getArgument('index'))
    {
      $arguments['index'] = $input->getArgument('index');
    }

    $this->executeSphinxCommand($output, 'indexer', $options, $arguments);
  }
}