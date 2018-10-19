<?php

namespace StoreBundle\Jstree;

/**
 * Description of CatalogSectionTreeListItemClientModel
 *
 * @author Dancy
 */
class CatalogSectionTreeListItemClientModel extends CatalogSectionAdminClientModel
{  
  public function getClientModelValues($context = null)
  {          
    $values = array(              
        'data'     => $this->section->getName(),
        'children' => $this->getChildCatalogSectionClientModelValues(),
        'metadata' => array('id' => $this->section->getId())
    );

    return $values;
  }


  public function getChildCatalogSectionClientModelValues()
  {
    $children = array();

    $childSections = $this->section->getChildren();          

    foreach ($childSections as $section)
    {
        $clientModel = new CatalogSectionTreeListItemClientModel($section);
        $children[] = $clientModel->getClientModelValues();
    }

    return $children;
  }
}
