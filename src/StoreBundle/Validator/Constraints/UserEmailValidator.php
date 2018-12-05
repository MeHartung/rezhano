<?php

namespace StoreBundle\Validator\Constraints;

use StoreBundle\Entity\User\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserEmailValidator extends ConstraintValidator
{
  /**
   * @param User $user
   * @param Constraint $constraint
   */
  public function validate ($user, Constraint $constraint)
  {
    if(!$user->getEmail())
    {
      $this->context->addViolation('Пользователь должен иметь email');
    }
    
    if($user->getEmail() && !filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL))
    {
      $this->context->addViolation('Пользователь должен иметь email вида example@mail.com.');
    }
  }
}