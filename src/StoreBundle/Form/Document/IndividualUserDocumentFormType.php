<?php

namespace StoreBundle\Form\Document;

use Doctrine\Common\Collections\ArrayCollection;

class IndividualUserDocumentFormType extends UserDocumentFormType
{
  protected function getTypes ()
  {
    return $this->documentTypeRepository->findForIndividual();
  }
}