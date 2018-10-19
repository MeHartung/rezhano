<?php

namespace StoreBundle\Form\Document;

use StoreBundle\Entity\Document\UserDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

/*
 * Форма для загрузки документа пользователя
 */
class UserDocumentType extends AbstractType
{
  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $documentName = $options['documentName'];
    $documentType = $options['documentType'];

    if (!$documentType instanceof \StoreBundle\Entity\Document\UserDocumentType)
    {
      throw new InvalidOptionsException(sprintf('documentType should be instance of \StoreBundle\Entity\Document\UserDocumentType'));
    }

    $subject = $builder->getData();

    $fileOptions = [
      'data' => null,
      'constraints' => [
        new NotNull()
      ],
      'data_class' => 'Symfony\Component\HttpFoundation\File\File',
    ];

    if ($subject instanceof UserDocument && $subject->getFile())
    {
      $fileOptions['sonata_help'] = $subject->getFile();
    }

    $builder
      ->add('name', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
        'data' => $documentName,
      ])
      ->add('documentType', null, [
        'data' => $documentType,
//        'disabled' => true,
        'attr' => [
          'readonly' => true,
          'style' => 'display:none;'
        ]
      ])
      ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', $fileOptions);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', 'StoreBundle\Entity\Document\UserDocument');
    //Требуем имя документа string
    $resolver->setRequired('documentName');
    //Требуем тип документа Entity\Document\UserDocumentType
    $resolver->setRequired('documentType');
  }
}