<?php

namespace StoreBundle\Meta;

use Accurateweb\MetaBundle\Model\MetaInterface;
use Accurateweb\TaxonomyBundle\Exception\TaxonNotFoundException;
use Accurateweb\TaxonomyBundle\Service\TaxonomyManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class MetaTaxon implements MetaInterface
{
  private $request;
  private $router;
  private $taxon;
  private $taxonomyManager;

  public function __construct (RequestStack $requestStack, TaxonomyManager $taxonomyManager, RouterInterface $router)
  {
    $this->request = $requestStack->getCurrentRequest();
    $this->router = $router;
    $this->taxonomyManager = $taxonomyManager;
  }

  private function getTaxon()
  {
    if (!$this->taxon && $this->request)
    {
      $alias = $this->request->get('slug');

      try
      {
        $this->taxon = $this->taxonomyManager->getTaxon($alias);
      }
      catch (TaxonNotFoundException $e)
      {

      }
    }

    return $this->taxon;
  }

  public function getMetaTitle ()
  {
    if (!$this->getTaxon())
    {
      return '';
    }

    return $this->getTaxon()->getName();
  }

  public function getMetaDescription ()
  {
    if (!$this->getTaxon())
    {
      return '';
    }

    return strip_tags($this->getTaxon()->getDescription());
  }

  public function getMetaKeywords ()
  {
    if (!$this->getTaxon())
    {
      return '';
    }

   return sprintf('%s, купить цены стоимость продажа', $this->getTaxon()->getName());
  }
}