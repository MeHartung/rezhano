<?php

namespace StoreBundle\Validator\Constraints;


use StoreBundle\Entity\User\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserRolesValidator extends ConstraintValidator
{
  /**
   * @param User $user
   * @param Constraint $constraint
   */
  public function validate ($user, Constraint $constraint)
  {
    $nbEquivalentRoles = 0;

    $nbEquivalentRoles += (int)$user->hasRole(User::ROLE_INDIVIDUAL);
    $nbEquivalentRoles += (int)$user->hasRole(User::ROLE_ENTREPRENEUR);
    $nbEquivalentRoles += (int)$user->hasRole(User::ROLE_JURIDICAL);

    if ($nbEquivalentRoles > 1)
    {
      $this->context->addViolation('Пользователь должен иметь только одну глобальную роль');
    }
  }
}