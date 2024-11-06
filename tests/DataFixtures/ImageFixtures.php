<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 07.02.18
 * Time: 18:55
 */

namespace Tests\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Catalog\Product\ProductImage;

class ImageFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {

    $product = $this->getReference('product-with-image');

    $imageOne = new ProductImage();
    $imageOne->setProduct($product);
    $imageOne->setPosition(0);
    $imageOne->setResourceId('test/path');
    
    $imageTwo = new ProductImage();
    $imageTwo->setProduct($product);
    $imageTwo->setPosition(1);
    $imageTwo->setResourceId('test/path');

    $imageThree = new ProductImage();
    $imageThree->setProduct($product);
    $imageThree->setPosition(2);
    $imageThree->setResourceId('test/path');
    
    $imageFour = new ProductImage();
    $imageFour->setProduct($product);
    $imageFour->setPosition(3);
    $imageFour->setResourceId('test/path');

    $manager->persist($imageOne);
    $manager->persist($imageTwo);
    $manager->persist($imageThree);
    $manager->persist($imageFour);

    $manager->flush();

    $this->addReference('product-with-image-img-0', $imageOne);
    $this->addReference('product-with-image-img-1', $imageTwo);
    $this->addReference('product-with-image-img-2', $imageThree);
    $this->addReference('product-with-image-img-3', $imageFour);
  }

}