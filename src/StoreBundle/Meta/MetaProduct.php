<?php

namespace StoreBundle\Meta;

use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use Accurateweb\MetaBundle\Model\MetaInterface;
use Accurateweb\MetaBundle\Model\MetaOpenGraphInterface;
use Accurateweb\MetaBundle\Model\OpenGraphType;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class MetaProduct implements MetaInterface, MetaOpenGraphInterface
{
  private $request;
  private $mediaStorage;
  private $router;
  /** @var Product */
  private $product;

  public function __construct (RequestStack $requestStack, ProductRepository $productRepository, MediaStorageInterface $mediaStorage, RouterInterface $router)
  {
    $this->request = $requestStack->getCurrentRequest();
    $this->mediaStorage = $mediaStorage;
    $this->router = $router;

    if ($this->request)
    {
      $alias = $this->request->get('slug');
      $this->product = $productRepository->findOneBy(['slug' => $alias]);
    }
  }

  public function getMetaTitle ()
  {
    if (!$this->product)
    {
      return '';
    }

    return $this->product->isBundle() ?
      sprintf('%s — %s — в Интернет-магазине сыроварни «Режано» с доставкой в Екатеринбурге, Реже', $this->product->getName(), mb_strtolower(strip_tags(str_replace(array("\r\n", "\r", "\n"),"",$this->product->getShortDescription()))))
      :
      sprintf('Купите сыр %s — %s — в Интернет-магазине сыроварни «Режано» с доставкой в Екатеринбурге, Реже', $this->product->getName(), mb_strtolower(strip_tags(str_replace(array("\r\n", "\r", "\n"),"",$this->product->getShortDescription()))));
  }

  public function getMetaDescription ()
  {
    if (!$this->product)
    {
      return '';
    }

    return $this->product->isBundle() ?
      sprintf('%s Вы можете заказать доставку товара %s в Интернет-магазине сыроварни «Режано»', strip_tags(str_replace(array("\r\n", "\r", "\n"),"",$this->product->getDescription())), $this->product->getName())
      :
      sprintf('%s Вы можете заказать доставку сыра %s в Интернет-магазине сыроварни «Режано»', strip_tags(str_replace(array("\r\n", "\r", "\n"),"",$this->product->getDescription())), $this->product->getName());
  }

  public function getMetaKeywords ()
  {
    if (!$this->product)
    {
      return null;
    }

    $taxon = $this->product->isBundle() ? $this->product->getPrimaryTaxon()->getStringName().' ' : '';

    $keywords = [
      $taxon.'сыр',
      'сыроварня',
      'сыровары',
      'купить сыр от сыроваров',
      'частная сыроварня',
      mb_strtolower(strip_tags(str_replace(array("\r\n", "\r", "\n"),"", $this->product->getShortDescription()))),
      'доставка свежего сыра',
    ];

//    foreach ($keywords as &$keyword)
//    {
//      $keyword = sprintf($keyword, $this->product->getName());
//    }

    return implode(', ', $keywords);
  }

  public function getTitle ()
  {
    if (!$this->product)
    {
      return null;
    }

    return $this->product->getName();
  }

  public function getType ()
  {
    return new OpenGraphType\OpenGraphTypeWebsite();
  }

  public function getImage ()
  {
    if (!$this->product)
    {
      return null;
    }

    $image = $this->product->getImage();
    $media = $image ? $this->mediaStorage->retrieve($image) : null;
    return $media?$media->getUrl():null;
  }

  public function getUrl ()
  {
    if (!$this->product)
    {
      return null;
    }

    return $this->router->generate('product', ['slug' => $this->product->getSlug()]);
  }

  public function getAudio ()
  {
    return null;
  }

  public function getDescription ()
  {
    return $this->product?strip_tags($this->product->getMetaDescription()):null;
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