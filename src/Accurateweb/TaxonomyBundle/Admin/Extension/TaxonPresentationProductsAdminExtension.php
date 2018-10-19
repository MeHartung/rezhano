<?php

namespace Accurateweb\TaxonomyBundle\Admin\Extension;

use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\PresentationOptions;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaxonPresentationProductsAdminExtension extends AbstractAdminExtension
{
  public function configureFormFields(FormMapper $formMapper)
  {
    /** @var Taxon $subject */
    $subject = $formMapper->getAdmin()->getSubject();

    if (null !== $subject->getPresentationId() && $subject->getPresentationId() !== TaxonPresentationInterface::TAXON_PRESENTATION_PRODUCTS)
    {
      return null;
    }

    //@TODO: Заменить на вызов PresentationOptionsResolver::resolve()
    $options = $subject->getPresentationOptions() ?: array();

    $formMapper
      ->tab('Дополнительно')
      ->add('showSubCategories', ChoiceType::class, [
        'choices' => [
          'Отображать подкатегории' => true,
          'Не отображать подкатегории' => false,
        ],
        'mapped' => false,
        'label' => 'Отображение подкатегорий',
        'data' => isset($options['showSubCategories']) ? $options['showSubCategories'] : false,
      ])
      ->add('showFilter', ChoiceType::class, [
        'choices' => [
          'Отображать фильтр' => true,
          'Не отображать фильтр' => false,
        ],
        'mapped' => false,
        'label' => 'Отображение фильтра',
        'data' => isset($options['showFilter']) ? $options['showFilter'] : false,
      ])
      ->end()
    ;
  }

  /**
   * @param AdminInterface $admin
   * @param $object Taxon
   */
  public function preUpdate (AdminInterface $admin, $object)
  {
    if ($object->getPresentationId() !== TaxonPresentationInterface::TAXON_PRESENTATION_PRODUCTS)
    {
      return null;
    }

    $showSubCategories = $admin->getForm()->get('showSubCategories')->getData();
    $showFilter = $admin->getForm()->get('showFilter')->getData();

    $options = $object->getPresentationOptions();

    if (!$options)
    {
      $options = new PresentationOptions();
      $options->setTaxon($object);
    }

    $options->setOptions([
      'showFilter' => $showFilter,
      'showSubCategories' => $showSubCategories,
    ]);

    $object->setPresentationOptions($options);
    parent::preUpdate($admin, $object);
  }


}