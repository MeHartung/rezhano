<?php

namespace StoreBundle\DataAdapter\Product;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
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
    $galleryImages = [];
    $thumbnails = [];

    foreach ($subject->getImages() as $image)
    { /** @var  $image Image */
      $images[] = $image->getResourceId();
    }

    foreach ($subject->getImages() as $image)
    {
      $thumb = $image->getThumbnail('570x713');

      if ($this->mediaStorage->exists($thumb))
      {
        $galleryImages[] = $this->mediaStorage->retrieve($thumb)->getUrl();
      }
    }

    /** @var ImageInterface $image */
    $image = $subject->getImages()->first();

    if ($image)
    {
      foreach ($image->getThumbnailDefinitions() as $thumbnail)
      {
        $thumb = $image->getThumbnail($thumbnail->getId());

        if ($this->mediaStorage->exists($thumb))
        {
          $thumbnails[$thumbnail->getId()] = $this->mediaStorage->retrieve($thumb)->getUrl();
        }
      }
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
      'gallery_images' => $galleryImages,
      'preview_image' => $subject->getFirstImage(), # вообще не юзается
      'taxon' => $primary_taxon?$primary_taxon->getName():'',
      'available_stock' => $subject->getAvailableStock(),
      'slug' => $subject->getSlug(),
      'name' => $subject->getName(),
      #'originalPrice' => $subject->getPrice(),
      'originalPrice' => $subject->getPrice(),
      'oldPrice' => $subject->getOldPrice(),
      'price' => $subject->getUnitPrice(),
      'measuredPartPrice' => $subject->getMeasuredPartPrice(),
     # TODO $subject->getUnitPrice() должно быть перенесено в priceManager
     # 'price' => $this->priceManager->getProductPrice($subject),
      'image' => $subject->getThumbnailUrl('catalog_prev'),
      'thumbnails' => $thumbnails,
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
      'type' => $this->productTypeAdapter->transform($subject->getProductType()),
      'bundle' => $subject->isBundle()
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