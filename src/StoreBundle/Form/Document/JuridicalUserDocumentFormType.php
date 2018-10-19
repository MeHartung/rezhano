<?php

namespace StoreBundle\Form\Document;

use Doctrine\Common\Collections\ArrayCollection;

class JuridicalUserDocumentFormType extends UserDocumentFormType
{
  protected function getTypes ()
  {
    return $this->documentTypeRepository->findForJuridical();
  }
}