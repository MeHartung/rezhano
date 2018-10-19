<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 15.09.17
 * Time: 14:37
 */
namespace StoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdsToModelTransformer implements DataTransformerInterface
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
   * Transforms an object (issue) to a string (number).
   *
   * @param Taxon[]|null $taxons
   * @return array
   */
  public function transform($taxons)
  {
     if (null === $taxons) {
      return array();
    }

    $ids = array();

    foreach ($taxons as $taxon)
    {
      $ids[] = $taxon->getId();
    }

    return $ids;
  }

  /**
   * Transforms a string (number) to an object (issue).
   *
   * @param  array $taxonIds
   * @return ArrayCollection|null
   * @throws TransformationFailedException if object (issue) is not found.
   */
  public function reverseTransform($taxonIds)
  {
    if (!$taxonIds) {
      return null;
    }

    $qb = $this->em
      ->getRepository(Taxon::class)
      ->createQueryBuilder('t');

    $qb->where($qb->expr()->in('t.id', $taxonIds));
    $taxons = $qb->getQuery()->getResult();

    return new ArrayCollection($taxons);
  }
}