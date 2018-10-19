<?php

namespace StoreBundle\Repository\Store\Catalog\Taxonomy;

use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use StoreBundle\Exception\Catalog\RootNodeNotFoundException;

/**
 * TaxonRepository
 *
 */
class TaxonRepository extends NestedTreeRepository
{
  /**
   * @return array
   */
  public function findTopMost()
  {
    return $this->findBy(array('treeLevel' => 1), ["treeLeft" => "ASC"]);
  }

  /**
   * Возвращает корневой раздел каталога
   *
   * @return Taxon
   * @throws RootNodeNotFoundException
   */
  public function getRootNode()
  {
    $rootNode = $this->getRootNodesQuery()
                     ->setMaxResults(1)
                     ->getOneOrNullResult();

    if (!$rootNode)
    {
      throw new RootNodeNotFoundException();
    }

    return $rootNode;
  }

  /**
   * @param $taxon Taxon
   * @return bool
   */
  public function getTaxonHasProducts(Taxon $taxon)
  {
    $taxonHasProducts = count($taxon->getProducts()) > 0;
    $taxonHasChildren = count($taxon->getChildren()) > 0;
    $taxonHasChildrenHasProducts = false;

    foreach ($taxon->getProducts() as $r)
    {
      $t[] = $r;
    }

    if($taxonHasChildren)
    {
      foreach ($taxon->getChildren() as $child)
      {
        $taxonHasChildrenHasProducts = count($child->getProducts()) > 0;
        if($taxonHasChildrenHasProducts)
        {
          break;
        }
      }
    }

    if ($taxonHasProducts || ($taxonHasChildren && $taxonHasChildrenHasProducts))
    {
      return true;
    }

    return false;

  }
}
