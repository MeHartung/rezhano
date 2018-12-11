<?php

namespace StoreBundle\Validator\Constraints;

use StoreBundle\Entity\Text\ContactPhone;
use StoreBundle\Repository\Store\Text\ContactPhoneRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContactPlaceValidator extends ConstraintValidator
{
  private $contactPhoneRepository;

  public function __construct (ContactPhoneRepository $contactPhoneRepository)
  {
    $this->contactPhoneRepository = $contactPhoneRepository;
  }

  /**
   * @param ContactPhone $value
   * @param Constraint $constraint
   */
  public function validate ($value, Constraint $constraint)
  {
    $place = $value->getShowPlace();

    if ($place !== ContactPhone::SHOW_PLACE_HIDE)
    {
      $query = $this->contactPhoneRepository
        ->createQueryBuilder('s')
        ->where('s.showPlace = :place')
        ->andWhere('s.published = true')
        ->setParameter('place', $place)
        ->setMaxResults(1);

      if ($value->getId())
      {
        $query
          ->andWhere('s != :store')
          ->setParameter('store', $value);
      }

      $existingPlace = $query
        ->getQuery()
        ->getOneOrNullResult();

      if ($existingPlace)
      {
        $this->context->addViolation($constraint->message);
      }
    }
  }
}