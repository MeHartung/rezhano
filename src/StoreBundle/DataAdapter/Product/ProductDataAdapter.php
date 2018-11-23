<?php

namespace StoreBundle\DataAdapter\Product;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\MediaBundle\Model\Media\Resource\MediaResource;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use Accurateweb\MediaBundle\Model\Thumbnail\ImageThumbnail;
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
  private $productTypeAdapter;

  public function __construct (RouterInterface $router, MediaStorageInterface $mediaStorage,
                               TokenStorageInterface $tokenStorage, ProductPriceManager $priceManager,
                               ProductTypeDataAdapter $productTypeDataAdapter)
  {
    $this->router = $router;
    $this->mediaStorage = $mediaStorage;
    $this->tokenStorage = $tokenStorage;
    $this->priceManager = $priceManager;
    $this->productTypeAdapter = $productTypeDataAdapter;
  }

  /**
   * @param $subject Product
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    #$image = $subject->getMainImage();
    #$image = $subject->getTeaserImageFileImage();
    #$media = $image ? $this->mediaStorage->retrieve($image) : null;
    $token = $this->tokenStorage->getToken();
    $isFavorite = false;
    $primary_taxon = $subject->getPrimaryTaxon();
    $brand = $subject->getBrand();
    $isMeasured = $subject->getMeasured();
    $count_step = $subject->getCountStep();
    $min_count = $subject->getMinCount();

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
      'preview_image' => $subject->getFirstImage(), # вообще не юзается
      'taxon' => $primary_taxon?$primary_taxon->getName():'',
      'available_stock' => $subject->getAvailableStock(),
      'slug' => $subject->getSlug(),
      'name' => $subject->getName(),
      'originalPrice' => $subject->getPrice(),
      'oldPrice' => $subject->getOldPrice(),
      'price' => $this->priceManager->getProductPrice($subject),
      'image' => $subject->getThumbnailUrl('catalog_prev'),
      'isPurchasable' => $subject->isPurchasable(),
      'isHit' => $subject->isHit(),
      'isNovice' => $subject->isNovice(),
      'isSale' => $subject->isSale(),
      'url' => $this->router->generate('product', array('slug' => $subject->getSlug())),
      'isFavorite' => $isFavorite,
      'background' => $subject->getBackground(),
      'package' => $subject->getFormattedPackage(),
      'units' => $subject->getUnits(),
      'isMeasured' => $isMeasured,
      'count_step' => $count_step,
      'min_count' => $min_count,
      'description' => $subject->getDescription(),
      'short_description' => htmlentities($subject->getShortDescription()),
      'attributes' => $subject->getProductAttributeValuesGrouped(),
      'type' => $this->productTypeAdapter->transform($subject->getProductType())
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