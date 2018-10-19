<?php

namespace AppBundle\DataAdapter\Form;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Symfony\Component\Form\FormInterface;

class FormErrorAdapter implements ClientApplicationModelAdapterInterface
{
  /**
   * @param $form FormInterface
   * @param array $options
   * @return array
   */
  public function transform ($form, $options = array())
  {
    $errors = array();

    foreach ($form->getErrors() as $key => $error)
    {
      if ($form->isRoot())
      {
        $errors['#'][] = $error->getMessage();
      }
      else
      {
        $errors[] = $error->getMessage();
      }
    }

    foreach ($form->all() as $child)
    {
      if (!$child->isValid())
      {
        $errors[$child->getName()] = $this->transform($child);
      }
    }

    return $errors;
  }

  public function getModelName ()
  {
    return 'FormErrors';
  }

  public function supports ($subject)
  {
    return $subject instanceof FormInterface;
  }
}