<?php

namespace AccurateCommerce\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class Pagination
{
  private $page;

  private $maxPerPage;

  private $pageCount;

  private $nbResults;

  public function __construct(QueryBuilder $queryBuilder, $page, $maxPerPage)
  {
    $this->page = $page;
    $this->maxPerPage = $maxPerPage;

    $pagedQueryBuilder = clone $queryBuilder
                                  ->setFirstResult($this->getOffset())
                                  ->setMaxResults($this->maxPerPage);

    $this->paginator = new Paginator($pagedQueryBuilder, false);

    $this->nbResults = count($this->paginator);
    $this->pageCount = ceil($this->nbResults / $this->maxPerPage);
  }

  public function getIterator()
  {
    return $this->paginator->getIterator();
  }

  public function getLastPage()
  {
    return $this->pageCount;
  }

  public function getOffset()
  {
    return ($this->page - 1)*$this->maxPerPage;
  }

  /**
   * @return mixed
   */
  public function getPage()
  {
    return $this->page;
  }

  /**
   * @return mixed
   */
  public function getMaxPerPage()
  {
    return $this->maxPerPage;
  }

  /**
   * @return float
   */
  public function getPageCount()
  {
    return $this->pageCount;
  }

  /**
   * @return int
   */
  public function getNbResults()
  {
    return $this->nbResults;
  }


  public function getFirstResult()
  {
    return $this->getOffset() + 1;
  }

  public function getLastResult()
  {
    return min($this->getNbResults(), $this->getFirstResult() + $this->getMaxPerPage() - 1);
  }
}