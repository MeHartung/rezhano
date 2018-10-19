<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 04.08.17
 * Time: 11:48
 */

namespace StoreBundle\Repository\Text;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SpecialOfferRepository extends EntityRepository
{
  public function findRecent($limit=3)
  {
    $qb = $this->createQueryBuilder('so');

    $qb->addOrderBy('so.dateStart');

    $this->filterByIsActive($qb);

    return $qb->getQuery()
              ->setMaxResults($limit)
              ->getResult();
  }

  /**
   * Фильтрует запрос по признаку активности акции
   *
   * @param QueryBuilder $qb Query builder
   * @param boolean $v true, если в выборку должны попасть только активные акции, или false, если в выборку должны попасть
   * только неактивные акции
   */
  public function filterByIsActive(QueryBuilder $qb, $v=true)
  {
    $now = date('Y-m-d');
    if ((bool)$v)
    {
      $qb->andWhere('so.dateStart <= :now AND (so.dateEnd >= :now OR so.dateEnd IS NULL)');
    }
    else
    {
      $qb->andWhere('so.dateStart < :now OR so.dateEnd > :now');
    }

    $qb->setParameter('now', $now);
  }
}