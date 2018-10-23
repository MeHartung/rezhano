<?php

namespace Tests\StoreBundle;

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\DataFixtures\CdekCityFixtures;
use StoreBundle\DataFixtures\ImageFixtures;
use StoreBundle\DataFixtures\NewsFixtures;
use StoreBundle\DataFixtures\OrderFixtures;
use StoreBundle\DataFixtures\OrderPaymentStatusFixture;
use StoreBundle\DataFixtures\OrderPaymentStatusTypeFixture;
use StoreBundle\DataFixtures\OrderStatusFixture;
use StoreBundle\DataFixtures\OrderStatusTypeFixture;
use StoreBundle\DataFixtures\PaymentMethodFixtures;
use StoreBundle\DataFixtures\ProductFixture;
use StoreBundle\DataFixtures\ShippingMethodFixture;
use StoreBundle\DataFixtures\TaxonFixture;
use StoreBundle\DataFixtures\UserFixtures;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\User\User;
use StoreBundle\Repository\Store\Order\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\FixtureAwareWebTestCase;

class StoreWebTestCase extends FixtureAwareWebTestCase
{
  /** @var Client */
  protected $client = null;
  /** @var User|null */
  protected $user;

  /** @var EntityManagerInterface */
  protected  $em;

  /** @var  */
  protected $application;

  /** @var Router */
  protected $router;

  protected function setUp ()
  {
    $this->client = $this->getClient(true);
    $this->em = $this->getEntityManager();
    $this->application = new Application($this->client->getKernel());
    $this->router = $this->client->getContainer()->get('router');

    parent::setUp();

    $this->addFixture(new CdekCityFixtures());
    $this->addFixture(new PaymentMethodFixtures());
    $this->addFixture(new TaxonFixture());
    $this->addFixture(new ProductFixture());
    $this->addFixture(new UserFixtures());
    $this->addFixture(new ImageFixtures());
    $this->addFixture(new OrderStatusTypeFixture());
    $this->addFixture(new OrderStatusFixture());
    $this->addFixture(new OrderPaymentStatusTypeFixture());
    $this->addFixture(new OrderPaymentStatusFixture());
    $this->executeFixtures();
  }

  protected function getClient($reload=false)
  {
    if (!$this->client)
    {
      $this->client = static::createClient();
    }

    return $this->client;
  }

  /**
   * @param User|null $user
   * @param array|null $roles
   */
  protected function logIn($user = null, $roles = null)
  {
    if (!$user)
    {
      $user = $this->getByReference('user-admin');
    }

    if (!$roles)
    {
      $roles = ['ROLE_SUPER_ADMIN'];
    }

    $this->user = $user;

    $session = $this->client->getContainer()->get('session');
    $firewallContext = 'main';

    $token = new UsernamePasswordToken($user, null, $firewallContext, $roles);
    $session->set('_security_' . $firewallContext, serialize($token));
    $session->save();

    $cookie = new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId());
    $this->client->getCookieJar()->set($cookie);
    $this->client->getContainer()->get('security.token_storage')->setToken($token);
  }

  /**
   * Заготовка
   * @param Application $application
   * @param $command
   * @param array $parameters
   */
  protected function commandRun($command, array $parameters = [])
  {
    $app = new Application($this->client->getKernel());

    $app->setAutoExit(false);

    $app->add($command);

    $parameters['command'] = !isset($parameters['command']) ?? $command->getName();

    $input = new ArrayInput($parameters);

    $command->run($input, new NullOutput());
  }


  protected function createOrder($user = null,
                                 $paymentMethod = null, $product = null,
                                 $quantity = null,
                                 $checkoutStateId = Order::CHECKOUT_STATE_COMPLETE)
  {
    $product = is_null($product) ? $this->getByReference('product-go-pro') : $product;
    $quantity = is_null($quantity) ? 1 : $quantity;
    $paymentMethod = is_null($paymentMethod) ? $this->getByReference('payment-cash') : $paymentMethod;

    $order = new Order();
    $order_item = new OrderItem();
    $order_item
      ->setProduct($product)
      ->setQuantity($quantity);

    $order
      ->addOrderItem($order_item)
      ->setPaymentMethod($paymentMethod)
      ->setCheckoutStateId($checkoutStateId)
      ->setUser($user);

    $documentNumberGenerator = $this->client->getContainer()->get('store.order.document_number_generator');
    $order->setDocumentNumber($documentNumberGenerator->generate());


    $this->em->persist($order);
    $this->em->flush();
    return $order;
  }

  protected function setCart($uid)
  {
    $this->appendFixture(new OrderFixtures());
    $this->client->getContainer()->get('store.user.cart')->invalidateCart();
    $this->client->getContainer()->get('session')->set('cart_id', $uid);
  }
}