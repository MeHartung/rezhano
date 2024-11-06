<?php

namespace StoreBundle\Jstree;

abstract class PluginCatalogSection /* extends BaseCatalogSection*/
{
  private $presentationProvider = null;

  public function __toString()
  {
    return $this->getName();
  }

  public function getDisplayName()
  {
    return $this->getName();
  }

  public function getSlug()
  {
    $alias = $this->getAlias();

    return (0 < strlen($alias)) ? $alias : 'section_' . $this->getId();
  }

  public function getProducts($criteria = null, $recursive = false)
  {
    return $this->getProductQuery($criteria, $recursive)->find();
  }

  public function getProductCount($criteria = null, $recursive = false)
  {
    return $this->getProductQuery($criteria, $recursive)->count();
  }

  public function getProductQuery($criteria = null, $recursive = false)
  {
    if ($recursive)
    {
      $catalogSections = $this->getDescendants();

      if (!($catalogSections instanceof PropelObjectCollection))
      {
        $catalogSections = new PropelObjectCollection();
      }
      $catalogSections->append($this);
    }
    else
    {
      $catalogSections = $this;
    }

    return ProductQuery::create(null, $criteria)
             ->useBindingQuery( )
               ->filterByCatalogSection($catalogSections)
             ->endUse( );
  }

  /**
   * Возращает используемое для раздела представление каталога
   * 
   * @param CatalogSectionPresentationProvider $provider
   * 
   * @return CatalogSectionPresentationBase
   */
  public function getPresentation(CatalogSectionPresentationProvider $provider)
  {
    $presentationId = $this->getPresentationId();
    return $provider->create($presentationId, $this);
  }

  public function getRoute(asBaseFilter $filter = null, $parameters = array())
  {
    return sprintf('@as_catalog_section?%s', http_build_query(array_merge($filter ? $filter->getParameters() : array(), $parameters, array('slug' => $this->getSlug()))));
  }

  public function generateCriteria(Criteria $c = null)
  {
    return $this->getProductQuery($c);
  }

  public function isVirtual()
  {
    return false;
  }

  public function getClientModelId()
  {
    return $this->getId();
  }

  public function getClientModelName()
  {
    return 'CatalogSection';
  }

  public function getClientModelValues($context = null)
  {
    $image = $this->getImage();
    $values = array(
      'id' => $this->getId(), 
      'name' => $this->getName(), 
      'catalog_id' => $this->getCatalogId(), 
      'url' => sfContext::getInstance()->getController()->genUrl(array('sf_route' => 'as_catalog_section', 'sf_subject' => $this)), 
      'alias' => $this->getAlias(),
      'image' => $image->file()->exists() ? $image->web()->url() : null,
      'presentation_id' => ($this->getPresentationId() ? $this->getPresentationId() : $this->getPresentationProvider()->getDefaultPresentation($this)->getId()), 
      'applicable_presentation_ids' => array_keys($this->getPresentationProvider()->getPresentationMap()->getNames()));

    if (is_array($context) && isset($context['with_children'])  && $context['with_children'])
    {
      $childrenArr = array();
      $children = $this->getChildren();
      foreach ($children as $catalogSection)
      {
        $childrenArr[] = $catalogSection->getClientModelValues($context);
        break;
      }

      $values['children'] = $childrenArr;
    }

    return $values;
  }

  public function getApplicablePresentationIds()
  {
    $applicablePresentationIds = array();
    $presentationMap = $this->getPresentationProvider()->getPresentationMap();
    $presentationIds = array_keys($presentationMap->getNames());
    foreach ($presentationIds as $presentationId)
    {
      $presentation = $presentationMap->create($presentationId, $this);

      if ($presentation->isApplicable())
      {
        $applicablePresentationIds[] = $presentationId;
      }
      unset($presentation);
    }

    return $applicablePresentationIds;
  }

  public function getPresentationProvider()
  {
    if (null === $this->presentationProvider)
    {
      $this->presentationProvider = new CatalogSectionPresentationProvider( );      
    }

    return $this->presentationProvider;
  }

  public function getKnownProperties(Criteria $criteria = null)
  {
    return ProductAttributeQuery::create(null, $criteria)
             ->useProductAttributeValueQuery()
               ->useProductAttributeValueToProductQuery()
                 ->useProductQuery()
                   ->useBindingQuery()
                     ->useCatalogSectionQuery()
                       ->filterByTreeLeft($this->getTreeLeft(), Criteria::GREATER_EQUAL) 
                       ->filterByTreeRight($this->getTreeRight(), Criteria::LESS_EQUAL)
                     ->endUse()
                   ->endUse()
                 ->endUse()
               ->endUse()
             ->endUse()
             ->groupBy('Id')
             ->find();
  }

  public function preSave(PropelPDO $con = null)
  {
    if (($this->getAlias() == '' && $this->getAlias() !== null))
    {
      $formatter = new sfTextFormatter( );

      $alias = $formatter->setString(($this->getName() ? $this->getName( ) : 'section_' . $this->getId( )))->formatTranslit()->formatLowercase()->getString();
      $count = CatalogSectionQuery::create()->filterByAlias($alias)->count();

      $this->setAlias($alias . ((0 < $count) ? '_' . $count : ''));
    }

    return parent::preSave($con);
  }

}
