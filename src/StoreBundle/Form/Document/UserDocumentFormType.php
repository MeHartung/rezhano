<?php

namespace StoreBundle\Form\Document;

use Doctrine\Common\Collections\ArrayCollection;
use StoreBundle\Entity\Document\UserDocument;
use StoreBundle\Repository\Document\UserDocumentTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*
 * Генерируем форму, со всеми документами, обязательными для данного типа пользователя
 */
abstract class UserDocumentFormType extends AbstractType
{
  protected $documentTypeRepository;

  public function __construct (UserDocumentTypeRepository $documentTypeRepository)
  {
    $this->documentTypeRepository = $documentTypeRepository;
  }

  public function buildForm (FormBuilderInterface $builder, array $options)
  {
    $types = $this->getTypes();
    $documents = $builder->getData();
    $docs = [];

    $builder->setData(null);

    if ($documents)
    {
      /** @var UserDocument $document */
      foreach ($documents as $document)
      {
        $docs[$document->getDocumentType()->getId()] = $document;
      }
    }

    foreach ($types as $i => $type)
    {
      $opts = [
        'documentName' => $type->getName(),
        'documentType' => $type,
        'required' => true,
        'mapped' => false,
        'label' => $type->getName(),
      ];

      if (isset($docs[$type->getId()]))
      {
        $opts['data'] = $docs[$type->getId()];
      }

      $builder->add($i, 'StoreBundle\Form\Document\UserDocumentType', $opts);
    }

    $builder->addEventListener(FormEvents::SUBMIT, [$this, 'postSetData']);
  }

  /*
   * Сами субмитим документы, т.к. ArrayCollection плохо работает с mapped
   */
  public function postSetData(FormEvent $event)
  {
    $datas = $event->getForm()->all();
    $documents = new ArrayCollection();

    foreach ($datas as $i => $form)
    {
      $documents->add($form->getData());
    }

    $event->setData($documents);
  }

  public function configureOptions (OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', 'Doctrine\Common\Collections\ArrayCollection');
    $resolver->setDefault('allow_extra_fields', true);
  }

  /**
   * @return \StoreBundle\Entity\Document\UserDocumentType[]|ArrayCollection
   */
  abstract protected function getTypes();
}