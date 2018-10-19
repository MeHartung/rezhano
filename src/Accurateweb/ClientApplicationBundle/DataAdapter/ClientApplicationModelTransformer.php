<?php

namespace Accurateweb\ClientApplicationBundle\DataAdapter;

class ClientApplicationModelTransformer
{
  /**
   * @var ClientApplicationModelAdapterManagerInterface
   */
  private $manager;

  public function __construct (ClientApplicationModelAdapterManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  /**
   * @param $subject
   * @param $adapter
   * @return array
   * @throws \Accurateweb\ClientApplicationBundle\Exception\NotFoundClientApplicationModelAdapterException
   * @throws \Exception
   */
  public function getClientModelData($subject, $adapter, $options=array())
  {
    $adapter = $this->manager->getModelAdapter($adapter);

    if (!$adapter->supports($subject))
    {
      throw new \Exception(sprintf('Adapter not support %s', get_class($subject)));
    }

    return $adapter->transform($subject, $options);
  }

  /**
   * @param $subjects
   * @param $adapter
   * @return array
   * @throws \Accurateweb\ClientApplicationBundle\Exception\NotFoundClientApplicationModelAdapterException
   * @throws \Exception
   */
  public function getClientModelCollectionData($subjects, $adapter, $options=array())
  {
    $data = array();

    foreach ($subjects as $subject)
    {
      $data[] = $this->getClientModelData($subject, $adapter, $options);
    }

    return  $data;
  }
}