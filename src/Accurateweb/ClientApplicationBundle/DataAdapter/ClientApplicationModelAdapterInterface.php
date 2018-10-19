<?php

namespace Accurateweb\ClientApplicationBundle\DataAdapter;

interface ClientApplicationModelAdapterInterface
{
  /**
   * @param $object
   * @param $options
   * @return array
   */
  public function transform($subject, $options=array());

  /**
   * @return string
   */
  public function getModelName();

  /**
   * @param $subject
   * @return boolean
   */
  public function supports($subject);
}