<?php

namespace Tests\StoreBundle\Unit\EventListener;

use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\User\User;
use Tests\DataFixtures\Catalog\ProductFixture;
use Tests\DataFixtures\Taxon\TaxonFixture;
use Tests\StoreBundle\StoreWebTestCase;

/**
 * @see OrderTotalCalculateSubscriber
 */
class OrderTotalCalculateSubscriberTest extends StoreWebTestCase
{
  protected function setUp ()
  {
    parent::setUp();
    $this->appendFixture(new TaxonFixture());
    $this->appendFixture(new ProductFixture());
  }

  public function testAddItem()
  {
    $cart = new Order();
    $item = new OrderItem();
    $item->setProduct($this->getReference('product'));
    $item->setQuantity(1);
    $cart->addOrderItem($item);
    $this->getEntityManager()->persist($cart);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(16000, $cart->getTotal());

    $item->setQuantity(2);
    $this->getEntityManager()->persist($item);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(32000, $cart->getTotal());

    $item = new OrderItem();
    $item->setProduct($this->getReference('product-hit'));
    $item->setQuantity(1);
    $cart->addOrderItem($item);
    $this->getEntityManager()->persist($item);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);


    $this->assertEquals(39000, $cart->getTotal());
  }

  public function testRemoveCartItem()
  {
    $cart = new Order();
    $item = new OrderItem();
    $item->setProduct($this->getReference('product'));
    $item->setQuantity(1);
    $item2 = new OrderItem();
    $item2->setProduct($this->getReference('product-hit'));
    $item2->setQuantity(1);

    $cart->addOrderItem($item);
    $cart->addOrderItem($item2);
    $this->getEntityManager()->persist($cart);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(23000, $cart->getTotal());

    $this->getEntityManager()->remove($item2);
    $this->getEntityManager()->flush();
    $this->getEntityManager()->refresh($cart);

    $this->assertEquals(16000, $cart->getTotal());
  }
}