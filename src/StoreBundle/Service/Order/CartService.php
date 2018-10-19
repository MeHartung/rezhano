<?php

namespace StoreBundle\Service\Order;

use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\User\User;
use StoreBundle\Event\CartItemEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */
class CartService
{
  /**
   * @var Session
   */
  private $session;

  /**
   * @var Order
   */
  private $cart;

  private $entityManager;

  private $token_storage;
  private $eventDispatcher;

  public function __construct(Session $session, EntityManager $em, TokenStorage $tokenStorage, EventDispatcherInterface $eventDispatcher)
  {
    $this->session = $session;
    $this->entityManager = $em;
    $this->token_storage = $tokenStorage;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * @return Order
   */
  public function getCart()
  {
    if (!$this->cart)
    {
      $cart = null;

      $cartUid = $this->session->get('cart_id');

      if ($cartUid)
      {
        $cart = $this->entityManager->getRepository('StoreBundle:Store\Order\Order')->findOneBy(['uid' => $cartUid]);
      }

      if (!$cart)
      {
        $cart = $this->createCart();
        $this->session->set('cart_id', (string)$cart->getUid());
      }

      $this->cart = $cart;
    }

    return $this->cart;
  }

  public function createCart()
  {
    $cart = new Order();

    $token = $this->token_storage->getToken();

    if ($token)
    {
      /** @var User $user */
      $user = $token->getUser();

      if ($user instanceof User)
      {
        $this->fromUser($cart, $user);
      }
    }

    $this->entityManager->persist($cart);
    $this->entityManager->flush();

    return $cart;
  }

  /**
   * Наполняем корзину данными пользователя
   * @param Order $cart
   * @param User $user
   */
  public function fromUser(Order $cart, User $user)
  {
    $cart->setCustomerEmail($user->getEmail());
    $cart->setCustomerFirstName($user->getFio());
//    $cart->setCustomerLastName($user->getLastName());
    $cart->setCustomerPhone($user->getPhone());
    $cart->setUser($user);

    $this->entityManager->persist($cart);
    $this->entityManager->flush();
  }

  public function resolve(Product $product)
  {
    $cart = $this->getCart();
    $cartItem = $cart->getOrderItem($product->getId());

    if (null === $cartItem)
    {
      $cartItem = new OrderItem();
      $cartItem->setProduct($product);
      $this->eventDispatcher->dispatch('cart.item.add', new CartItemEvent($cartItem));
      $cart->addOrderItem($cartItem);
    }

    return $cartItem;
  }

  public function invalidateCart()
  {
    $this->cart = null;
    $this->session->remove('cart_id');
  }

  public function canChange(OrderItem $item)
  {
    $order = $item->getOrder();

    return $order->getCheckoutStateId() <= Order::CHECKOUT_STATE_CART && $order->getId() == $this->getCart()->getId();
  }
}