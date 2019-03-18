<?php


namespace StoreBundle\Model\Navigate;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;

/**
 * Class SortableEntityNavigate
 * Позволяет получить след. и предыдущий элементы
 */
class SortableEntityNavigate
{
  /**
   * @var QueryBuilder $qb
   */
  protected $qb;
  protected $current;
  
  public function __construct(EntityRepository $repository, $currentItem)
  {
    if (!is_object($currentItem))
    {
      throw new \InvalidArgumentException('Expected $entity to be an object');
    }
    
    if (!method_exists($currentItem, 'getPosition'))
    {
      throw new \InvalidArgumentException('Object must have getPosition method!');
    }
    
    $currentItemClass = get_class($currentItem);
    if($currentItemClass !== $repository->getClassName())
    {
      throw new \LogicException("Unsuitable repository class
      {$repository->getClassName()} for type object {$currentItemClass}");
    }
    
    $this->current = $currentItem;
    
    $this->qb = $repository->createQueryBuilder('cs')->setMaxResults(1);
  }
  
  /**
   *
   * @return null|mixed
   * @throws ORMException
   */
  public function getPast()
  {
    $qb = clone $this->qb;
    return $this->executeQuery($qb->andWhere('cs.position < :currPosition'), 'DESC');
  }
  
  /**
   * Текущая заметка
   *
   * @return mixed
   */
  public function getCurrent()
  {
    return $this->current;
  }
  
  /**
   * @return mixed|null
   * @throws ORMException
   */
  public function getNext()
  {
    $qb = clone $this->qb;
    return $this->executeQuery($qb->andWhere('cs.position > :currPosition'), 'ASC');
  }
  
  /**
   * @param QueryBuilder $qb
   * @param string $order
   * @return mixed|null
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  protected function executeQuery(QueryBuilder $qb, $order = 'ASC')
  {
    return $qb
      ->setParameter('currPosition', $this->getCurrent()->getPosition())
      ->orderBy('cs.position', $order)
      ->getQuery()->getOneOrNullResult();
  }
  
  protected function getQueryBuilder(): ?QueryBuilder
  {
    return $this->qb;
  }
}