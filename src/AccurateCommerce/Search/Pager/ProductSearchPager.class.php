<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

/**
 * Description of ProductSearchPager
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ProductSearchPager extends sfProductPager
{
  private $productSearch,
          $offset,
          $limit;
  
  public function __construct(ProductSearch $productSearch, ModelCriteria $query, $maxPerPage = 10)
  {
    $this->productSearch = $productSearch;
    
    parent::__construct($query, $maxPerPage);
  }
  
  public function init($con = null)
  {
    $this->con = $con;
    $hasMaxRecordLimit = ($this->getMaxRecordLimit() !== false);
    $maxRecordLimit = $this->getMaxRecordLimit();

    $qForCount = clone $this->getQuery();
    $count = $qForCount
        ->offset(0)
        ->limit(0)
        ->count($this->con);

    $this->setNbResults($hasMaxRecordLimit ? min($count, $maxRecordLimit) : $count);
    
    $offset = 0;
    $limit = $this->getMaxPerPage();
    
    if (($this->getPage() == 0 || $this->getMaxPerPage() == 0)) {
        $this->setLastPage(0);
    } else {
        $this->setLastPage((int) ceil($this->getNbResults() / $this->getMaxPerPage()));
        
        $offset = ($this->getPage() - 1) * $this->getMaxPerPage();        
        if ($hasMaxRecordLimit) {
            $maxRecordLimit = $maxRecordLimit - $offset;
            if ($maxRecordLimit <= $this->getMaxPerPage()) {
                $limit = $maxRecordLimit;
            }
        }        
    }

    $this->offset = $offset;
    $this->limit = $limit;
    
    if (!$this->productSearch->getSortByRelevance())
    {
      $q = $this->getQuery();
      $q->offset($this->offset)
        ->limit($this->limit);
    }
  }
  
  public function getResults()
  {
    if (null === $this->results)
    {
      if ($this->productSearch->getSortByRelevance())
      {
        $qForIds = clone $this->getQuery();
        
        $foundSphinxObjectIds = $this->productSearch->getObjectIds();
        $filteredProductIds = $qForIds->clearSelectColumns()
                                      ->select('Id')
                                      ->find()
                                      ->getArrayCopy();
        
        $productIds = array_slice(array_intersect($foundSphinxObjectIds, $filteredProductIds), 
                $this->offset, $this->limit);
        
        $qForResults = clone $this->getQuery();
        $qForResults->addAnd(ProductPeer::ID, $productIds, Criteria::IN);
                
        $products = $qForResults->find()->getArrayCopy();
        
        $weightMap = $this->productSearch->getWeightMap();
        foreach ($products as $product)
        {
          if (isset($weightMap[$product->getId()]))
          {
            $product->setSphinxWeight($weightMap[$product->getId()] + ($product->getIsPurchasable() ? 100000 : 0));
          }
        }

        $this->productSearch->sortByRelevance($products);
        
        $productCollection = new PropelObjectCollection();
        $productCollection->setModel('Product');
        $productCollection->setData($products);

        return $productCollection;        
      }
      else
      {
        return parent::getResults();
      }
    }
    
    return $this->results;
  }
}
