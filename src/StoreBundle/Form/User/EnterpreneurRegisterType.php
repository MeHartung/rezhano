<?php

namespace StoreBundle\Form\User;


use StoreBundle\Entity\User\User;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class EnterpreneurRegisterType extends RegisterType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder->remove('roles');
    $builder->add('roles', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
      'data' => User::ROLE_ENTREPRENEUR
    ]);

    $builder->get('roles')->resetModelTransformers()
      ->addModelTransformer(new CallbackTransformer([$this, 'transformRoles'], [$this, 'reverseTransformRoles']));
    $builder->add('documents', 'StoreBundle\Form\Document\EntrepreneurUserDocumentFormType');
  }

  public function getBlockPrefix ()
  {
    return 'registerEnterpreneur';
  }

}