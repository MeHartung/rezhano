<?php

namespace Accurateweb\ClientApplicationBundle\DataAdapter;

use Accurateweb\ClientApplicationBundle\Exception\NotFoundClientApplicationModelAdapterException;

class ClientApplicationModelAdapterManager implements ClientApplicationModelAdapterManagerInterface
{
  private $adapters;

  public function addModelAdapter(ClientApplicationModelAdapterInterface $adapter, $alias)
  {
    $this->adapters[$alias] = $adapter;
  }

  /**
   * @param $alias
   * @return mixed
   * @throws NotFoundClientApplicationModelAdapterException
   */
  public function getModelAdapter($alias)
  {
    if (!isset($this->adapters[$alias]))
    {
      throw new NotFoundClientApplicationModelAdapterException(sprintf('Not found ModelAdapter with alias %s', $alias));
    }

    return $this->adapters[$alias];
  }

  //@TODO нужен ли метод для поиска первого попавшегося адаптера через supports?
}