<?php

namespace StoreBundle\Validator\Constraints;

use StoreBundle\Entity\User\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserPasswordValidator extends ConstraintValidator
{
  /**
   * @param User $user
   * @param Constraint $constraint
   */
  public function validate ($user, Constraint $constraint)
  {
    if(!$user->getId() && !$user->getPlainPassword())
    {
      $this->context->addViolation('Пользователь должен иметь пароль!');
    }
  }
}