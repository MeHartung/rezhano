<?php

namespace StoreBundle\Form\Document;

use Doctrine\Common\Collections\ArrayCollection;

class EntrepreneurUserDocumentFormType extends UserDocumentFormType
{
  protected function getTypes ()
  {
    return $this->documentTypeRepository->findForEnterpreneur();
  }
}