<?php

namespace AppBundle\Meta;

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

    return sprintf('%s, купить по низким ценам с доставкой по РФ', $this->product->getName());
  }

  public function getMetaDescription ()
  {
    if (!$this->product)
    {
      return '';
    }

    return sprintf('%s по низким ценам, Купить с доставкой по РФ', $this->product->getName(), $this->product->getName());
  }

  public function getMetaKeywords ()
  {
    if (!$this->product)
    {
      return null;
    }

    $keywords = [
      '%s купить',
      '%s цена',
      '%s Екатеринбург',
      '%s интернет магазин',
      '%s продажа',
      '%s заказ',
      '%s доставка',
      '%s в подарок',
      '%s в кредит',
      '%s аксессуары',
      '%s отзывы',
      '%s описание',
      '%s фото',
      '%s инструкции'
    ];

    foreach ($keywords as &$keyword)
    {
      $keyword = sprintf($keyword, $this->product->getName());
    }

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
    return $this->product?$this->product->getMetaDescription():null;
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