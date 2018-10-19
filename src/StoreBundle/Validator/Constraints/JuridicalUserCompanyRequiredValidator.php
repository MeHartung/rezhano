<?php

namespace StoreBundle\Validator\Constraints;


use StoreBundle\Entity\User\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JuridicalUserCompanyRequiredValidator extends ConstraintValidator
{
  /**
   * @param User $user
   * @param Constraint $constraint
   */
  public function validate ($user, Constraint $constraint)
  {
    if (!$user->hasRole(User::ROLE_JURIDICAL))
    {
      return;
    }

    if (!$user->getCompany())
    {
      $this->context->addViolation('Для юридического лица должны быть указаны данные компании');
    }
  }
}