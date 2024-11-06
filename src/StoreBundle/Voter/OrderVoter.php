<?php

namespace StoreBundle\Voter;

use Doctrine\ORM\EntityManager;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderVoter extends Voter
{
  const VIEW = 'view';

  protected function supports ($attribute, $subject)
  {
    if (!in_array($attribute, array(self::VIEW)))
    {
      return false;
    }

    if (!$subject instanceof Order)
    {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute ($attribute, $order, TokenInterface $token)
  {
    $user = $token->getUser();

    if (!$user instanceof User)
    {
      return false;
    }

    return $order->getUser() == $user;
  }
}