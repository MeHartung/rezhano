<?php

namespace Accurateweb\ClientApplicationBundle\DataAdapter;

use Accurateweb\ClientApplicationBundle\Exception\NotFoundClientApplicationModelAdapterException;

interface ClientApplicationModelAdapterManagerInterface
{
  public function addModelAdapter(ClientApplicationModelAdapterInterface $adapter, $alias);

  /**
   * @param $alias
   * @return ClientApplicationModelAdapterInterface
   * @throws NotFoundClientApplicationModelAdapterException
   */
  public function getModelAdapter($alias);
}