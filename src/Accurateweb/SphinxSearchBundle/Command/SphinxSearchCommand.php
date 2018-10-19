<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 17.06.2017
 * Time: 21:13
 */

namespace Accurateweb\SphinxSearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SphinxSearchCommand extends ContainerAwareCommand
{
  protected function buildParameters($parameters)
  {
    $paramsArray = array();
    foreach ($parameters as $name => $value)
    {
      if ($value !== true)
      {
        $paramsArray[] = sprintf('--%s %s', $name, $value);
      }
      else
      {
        $paramsArray[] = sprintf('--%s', $name);
      };
    }

    return implode(' ', $paramsArray);
  }

  /**
   * @param OutputInterface $output
   * @param $command A command to execute
   * @param $options
   * @param array $arguments
   */
  protected function executeSphinxCommand(OutputInterface $output, $command, $options, $arguments = array())
  {
    $searchdOptions = $this->getContainer()->get('accurateweb.sphinxsearch')->getSearchdOptions();

    $binaryPath = $searchdOptions->getBinaryPath();

    $command = sprintf('%s%s %s', $binaryPath, $command, $this->buildParameters($options));

    if (!empty($arguments))
    {
      $command .= ' ' . implode(' ', $arguments);
    }

    $output->writeln($command);

    passthru($command);
  }
}