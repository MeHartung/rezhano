<?php

namespace StoreBundle\Repository\Document;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class UserDocumentTypeRepository extends SortableRepository
{
  public function findForJuridical()
  {
    return $this->findBy(['showJuridical' => true], ['positionJuridical' => 'asc']);
  }

  public function findForIndividual()
  {
    return $this->findBy(['showIndividual' => true], ['positionIndividual' => 'asc']);
  }

  public function findForEnterpreneur()
  {
    return $this->findBy(['showEnterpreneur' => true], ['positionEnterpreneur' => 'asc']);
  }
}