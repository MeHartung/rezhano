<?php

namespace StoreBundle\Validator\Constraints;


use StoreBundle\Entity\Store\Catalog\Product\Product;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductPreorderValidator extends ConstraintValidator
{
  /**
   * @param Product $product
   * @param Constraint $constraint
   */
  public function validate($product, Constraint $constraint)
  {
    $is_preorder = $product->isPreorder();
    $has_expected_delivery_date = $product->getExpectedDeliveryDate() != null;

    /** Отвалидируем заголовк и анонс */
    if ($is_preorder && !$has_expected_delivery_date)
    {
      $this->context->buildViolation($constraint->message)
        ->addViolation();
    }
  }


}