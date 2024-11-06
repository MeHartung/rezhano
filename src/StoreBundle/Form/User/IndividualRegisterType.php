<?php

namespace StoreBundle\Form\User;


use StoreBundle\Entity\User\User;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class IndividualRegisterType extends RegisterType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder->remove('roles');
    $builder->add('roles', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
      'data' => User::ROLE_INDIVIDUAL
    ]);
    $builder->add('contragent');

    $builder->get('roles')->resetModelTransformers()
      ->addModelTransformer(new CallbackTransformer([$this, 'transformRoles'], [$this, 'reverseTransformRoles']));
    $builder->add('documents', 'StoreBundle\Form\Document\IndividualUserDocumentFormType');
  }

  public function getBlockPrefix ()
  {
    return 'registerIndividual';
  }
}