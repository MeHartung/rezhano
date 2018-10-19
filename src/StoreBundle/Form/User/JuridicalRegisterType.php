<?php

namespace StoreBundle\Form\User;


use StoreBundle\Entity\User\User;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class JuridicalRegisterType extends RegisterType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
    $builder->add('company', 'StoreBundle\Form\User\CompanyFormType');
    $builder->remove('roles');
    $builder->add('roles', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
      'data' => User::ROLE_JURIDICAL
    ]);
    $builder->add('contragent');

    $builder->get('roles')->resetModelTransformers()
      ->addModelTransformer(new CallbackTransformer([$this, 'transformRoles'], [$this, 'reverseTransformRoles']));
    $builder->add('documents', 'StoreBundle\Form\Document\JuridicalUserDocumentFormType');
  }

  public function getBlockPrefix ()
  {
    return 'registerjuridical';
  }
}