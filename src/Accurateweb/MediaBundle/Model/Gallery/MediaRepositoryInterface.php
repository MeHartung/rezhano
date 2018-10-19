<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Model\Gallery;

interface MediaRepositoryInterface
{
  public function getAll();

  public function find($id);
}