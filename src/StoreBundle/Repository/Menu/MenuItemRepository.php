<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 02.08.17
 * Time: 16:28
 */
namespace StoreBundle\Repository\Menu;

use StoreBundle\Entity\Menu\MenuItem;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class MenuItemRepository extends NestedTreeRepository
{
  /**
   * @return array
   */
  public function findTopMost()
  {
    return $this->findBy(array('treeLevel' => 1));
  }

  /**
   * Возвращает корневой раздел каталога
   *
   * @return MenuItem
   */
  public function getRootNode()
  {
    return $this
             ->getRootNodesQuery()
             ->getOneOrNullResult();
  }
}