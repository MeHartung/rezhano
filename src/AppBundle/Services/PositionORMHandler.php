<?php

namespace AppBundle\Services;

use Doctrine\Common\Util\ClassUtils;
use Pix\SortableBehaviorBundle\Services\PositionORMHandler as BasePositionORMHandler;

class PositionORMHandler extends BasePositionORMHandler
{
  private static $cacheLastPosition;

  public function getLastPosition ($entity)
  {
    $entityClass = ClassUtils::getClass($entity);
    $groups = $this->getSortableGroupsFieldByEntity($entityClass);
    $cacheKey = $this->getCacheKeyForLastPosition($entity, $groups);

    if (!isset(self::$cacheLastPosition[$cacheKey])) {
      $qb = $this->em->createQueryBuilder()
        ->select(sprintf('MAX(t.%s) as last_position', $this->getPositionFieldByEntity($entityClass)))
        ->from($entityClass, 't')
      ;

      if ($groups) {
        $i = 1;
        foreach ($groups as $groupName) {
          $getter = 'get' . $groupName;

          if ($entity->$getter()) {
            $qb
              ->andWhere(sprintf('t.%s = :group_%s', $groupName, $i))
              ->setParameter(sprintf('group_%s', $i), $entity->$getter())
            ;
            $i++;
          }
        }
      }

      self::$cacheLastPosition[$cacheKey] = (int)$qb->getQuery()->getSingleScalarResult();
    }

    return self::$cacheLastPosition[$cacheKey];
  }

  private function getCacheKeyForLastPosition($entity, $groups)
  {
    $cacheKey = ClassUtils::getClass($entity);

    foreach ($groups as $groupName)
    {
      $getter = 'get' . $groupName;

      if ($entity->$getter())
      {
        $cacheKey .= '_' . $entity->$getter()->getId();
      }
    }

    return $cacheKey;
  }
}