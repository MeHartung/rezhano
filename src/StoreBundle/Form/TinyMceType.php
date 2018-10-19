<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TinyMceType extends AbstractType
{
  public function getParent()
  {
    return TextareaType::class;
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefined(['custom_buttons']);
  }

  public function buildView (FormView $view, FormInterface $form, array $options)
  {
    $view->vars['custom_buttons'] = isset($options['custom_buttons'])?$options['custom_buttons']:[];
  }
}