<?php

namespace Accurateweb\MoyskladIntegrationBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use MoySklad\Components\FilterQuery;
use MoySklad\Components\Specs\QuerySpecs\QuerySpecs;
use MoySklad\Entities\AbstractEntity;
use MoySklad\MoySklad;

class MoyskladRepository implements ObjectRepository
{
  private $sklad;
  private $class;

  public function __construct (Moysklad $moysklad, $class)
  {
    $this->sklad = $moysklad;
    $this->class = $class;

    if (!class_exists($class))
    {
      throw new \InvalidArgumentException(sprintf('Class %s not found', $class));
    }

    $entity = new $class($moysklad);

    if (!$entity instanceof AbstractEntity)
    {
      throw new \InvalidArgumentException(sprintf('Class %s not instanceof AbstractEntity'));
    }
  }

  public function find ($id)
  {
    return $this->findOneBy(['id' => $id]);
  }

  public function findAll ()
  {
    $class = $this->class;
    $list = $class::query($this->sklad)->getList();
    return $list;
  }

  public function findBy (array $criteria, array $orderBy = null, $limit = null, $offset = null)
  {
    $class = $this->class;
    $filter = new FilterQuery();

    foreach ($criteria as $field => $value)
    {
      $filter
        ->eq($field, $value);
    }
    
    $list = $class::query($this->sklad, QuerySpecs::create([
      "offset" => $offset,
      "maxResults" => $limit,
    ]))->filter($filter);
    
    return $list;
  }

  public function findOneBy (array $criteria)
  {
    $list = $this->findBy($criteria, null, 1, 0);
    return isset($list[0])?$list[0]:null;
  }

  public function getClassName ()
  {
    return $this->class;
  }

  /**
   * @return \stdClass
   */
  public function getClassMetadata()
  {
    $class = $this->class;
    return $class::getMetaData($this->sklad);
  }

  /**
   * @return AbstractEntity
   */
  public function createNewObject($fields=[])
  {
    $class = $this->class;
    $obj = new $class($this->sklad, $fields);
    return $obj->create();
  }

}