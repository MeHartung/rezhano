<?php
/**
 * Created by PhpStorm.
 * User: Dancy
 * Date: 18.09.2017
 * Time: 22:42
 */

namespace StoreBundle\DataExport;


use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Repository\Store\Catalog\Product\ProductRepository;
use StoreBundle\Repository\Store\Catalog\Taxonomy\TaxonRepository;
use Symfony\Component\Routing\Router;

class YandexMarketXmlBuilder
{
  private $taxonRepository;

  private $productRepository;

  private $router;

  private $mediaStorage;
  /** @var \Twig_Environment  */
  private $twig;

  public function __construct(TaxonRepository $taxonRepository, ProductRepository $productRepository,
    Router $router, MediaStorageInterface $mediaStorage, \Twig_Environment $twig)
  {
    $this->taxonRepository = $taxonRepository;
    $this->productRepository = $productRepository;
    $this->router = $router;
    $this->mediaStorage = $mediaStorage;
    $this->twig = $twig;
  }

  public function build()
  {
    $taxons = $taxons = $this->taxonRepository
      ->createQueryBuilder('t')
      ->where('t.treeLeft > 1')
      ->getQuery()
      ->getResult();

    $products = $this
      ->productRepository
      ->createQueryBuilder('p')
      ->join('p.taxons', 't')
      ->where('p.published > 0 and p.isPurchasable > 0 and p.allowedForYandexMarket > 0')
      ->getQuery()
      ->getResult();

    return $this->twig->render('@Store/Yandex/price.xml.twig', [
      'taxons' => $taxons,
      'products' => $products,
      'original_url' => 'https://www.Store.ru'
    ]);
  }
}