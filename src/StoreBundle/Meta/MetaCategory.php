<?php

namespace StoreBundle\Meta;

use Accurateweb\MetaBundle\Model\MetaInterface;
use Accurateweb\MetaBundle\Model\MetaOpenGraphInterface;
use Accurateweb\MetaBundle\Model\OpenGraphType\OpenGraphTypeWebsite;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class MetaCategory implements MetaInterface, MetaOpenGraphInterface
{
  private $request;
  private $router;
  /** @var Taxon */
  private $category;

  public function __construct (RequestStack $requestStack, TaxonRepository $categoryRepository, RouterInterface $router)
  {
    $this->request = $requestStack->getCurrentRequest();
    $this->router = $router;

    if ($this->request)
    {
      $alias = $this->request->get('slug');
      $this->category = $categoryRepository->findOneBy(['slug' => $alias]);
    }
  }

  public function getMetaTitle ()
  {
    if (!$this->category)
    {
      return '';
    }

    return sprintf('Купить %s по низким ценам', $this->category->getName());
  }

  public function getMetaDescription ()
  {
    if (!$this->category)
    {
      return '';
    }

    return sprintf('Предлагаем выбрать и купить %s по низким ценам', $this->category->getName());
  }

  public function getMetaKeywords ()
  {
    if (!$this->category)
    {
      return null;
    }

    return sprintf('%s, купить цены стоимость продажа', $this->category->getName());
  }

  public function getTitle ()
  {
    return $this->getMetaTitle();
  }

  public function getType ()
  {
    return new OpenGraphTypeWebsite();
  }

  public function getImage ()
  {
    return null;
  }

  public function getUrl ()
  {
    if (!$this->category)
    {
      return null;
    }

    return $this->router->generate('taxon', ['slug' => $this->category->getSlug()]);
  }

  public function getAudio ()
  {
    return null;
  }

  public function getDescription ()
  {
    return $this->getMetaDescription();
  }

  public function getDeterminer ()
  {
    return null;
  }

  public function getLocale ()
  {
    return null;
  }

  public function getLocaleAlternate ()
  {
    return null;
  }

  public function getSiteName ()
  {
    return null;
  }

  public function getVideo ()
  {
    return null;
  }


}