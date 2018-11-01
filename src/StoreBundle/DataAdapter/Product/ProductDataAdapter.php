<?php

namespace StoreBundle\DataAdapter\Product;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\MediaBundle\Model\Media\Resource\MediaResource;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\User\User;
use StoreBundle\Service\Product\ProductPrice\ProductPriceManager;
use StoreBundle\Util\DateFormatter;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductDataAdapter implements ClientApplicationModelAdapterInterface
{
  private $router;
  private $mediaStorage;
  private $tokenStorage;
  private $priceManager;

  public function __construct (RouterInterface $router, MediaStorageInterface $mediaStorage, TokenStorageInterface $tokenStorage, ProductPriceManager $priceManager)
  {
    $this->router = $router;
    $this->mediaStorage = $mediaStorage;
    $this->tokenStorage = $tokenStorage;
    $this->priceManager = $priceManager;
  }

  /**
   * @param $subject Product
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $image = $subject->getMainImage();
    $media = $image ? $this->mediaStorage->retrieve($image) : null;
    $token = $this->tokenStorage->getToken();
    $isFavorite = false;
    $primary_taxon = $subject->getPrimaryTaxon();
    $brand = $subject->getBrand();
    $isMeasured = $subject->getMeasured();
    $count_step = $subject->getMinCount();
    $min_count = $subject->getCountStep();

    $images = [];
    foreach ($subject->getImages() as $image)
    { /** @var  $image Image */
      $images[] = $image->getResourceId();
    }

    if ($token)
    {
      $user = $token->getUser();

      if ($user instanceof User)
      {
        $favoriteList = $user->getFavoriteProductList();
        $isFavorite = $favoriteList->getProducts()->contains($subject);
      }
    }

    /**
     * @var $media MediaResource
     */
    return array(
      'id' => $subject->getId(),
      'brand' => $brand ? $brand->getName() : null,
      'sku' => $subject->getSku(),
      'images' => count($images)>0 ? $images : null,
      'preview_image' => $subject->getFirstImage(),
      'taxon' => $primary_taxon?$primary_taxon->getName():'',
      'available_stock' => $subject->getAvailableStock(),
      'slug' => $subject->getSlug(),
      'name' => $subject->getName(),
      'originalPrice' => $subject->getPrice(),
      'oldPrice' => $subject->getOldPrice(),
      'price' => $this->priceManager->getProductPrice($subject),
      'image' => $media ? $media->getUrl() : null,
      'isPurchasable' => $subject->isPurchasable(),
      'isHit' => $subject->isHit(),
      'isNovice' => $subject->isNovice(),
      'isSale' => $subject->isSale(),
      'url' => $this->router->generate('product', array('slug' => $subject->getSlug())),
      'isFavorite' => $isFavorite,
      'background' => $subject->getBackground(),
      'package' => $subject->getPackage(),
      'units' => $subject->getUnits(),
      'isMeasured' => $isMeasured,
      'count_step' => $count_step,
      'min_count' => $min_count,
      'description' => $subject->getDescription(),
      'attributes' => $subject->getProductAttributeValuesGrouped()
    );
  }

  public function getModelName ()
  {
    return 'Product';
  }

  public function supports ($subject)
  {
    return $subject instanceof Product;
  }
}