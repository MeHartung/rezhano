<?php

namespace StoreBundle\Voter;

use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Resolver\Product\ProductPublicationManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductPublicationVoter extends Voter
{
  private $productPublicationManager;

  public function __construct (ProductPublicationManager $productPublicationManager)
  {
    $this->productPublicationManager = $productPublicationManager;
  }

  protected function supports ($attribute, $subject)
  {
    return ($attribute === 'publication')
      && $subject instanceof Product;
  }

  protected function voteOnAttribute ($attribute, $subject, TokenInterface $token)
  {
    return $this->productPublicationManager->canPublish($subject);
  }
}