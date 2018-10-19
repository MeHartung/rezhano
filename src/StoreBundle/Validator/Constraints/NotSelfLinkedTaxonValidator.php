<?php

namespace StoreBundle\Validator\Constraints;


use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotSelfLinkedTaxonValidator extends ConstraintValidator
{
  /**
   * @param Taxon $taxon
   * @param Constraint $constraint
   */
  public function validate ($taxon, Constraint $constraint)
  {
    $linked = $taxon->getLinkedTaxons();

    if (!$linked)
    {
      return;
    }

    foreach ($linked as $item)
    {
      if ($item->getId() === $taxon->getId())
      {
        $this->context->addViolation('Раздел не может ссылаться на себя');
      }
    }
  }
}