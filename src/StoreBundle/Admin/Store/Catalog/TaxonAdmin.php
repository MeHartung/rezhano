<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 31.07.17
 * Time: 18:28
 */

namespace StoreBundle\Admin\Store\Catalog;

use Accurateweb\MediaBundle\Form\ImageType;
use Accurateweb\TaxonomyBundle\Model\TaxonPresentation\TaxonPresentationInterface;
use StoreBundle\Form\TinyMceType;
use RedCode\TreeBundle\Admin\AbstractTreeAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaxonAdmin extends AbstractTreeAdmin
{

  protected $translationDomain = 'messages';

  protected $listModes = [
    'tree' => array(
      'class' => 'fa fa-list fa-fw',
    ),

  ];
  protected function configureListFields(ListMapper $listMapper)
  {

    $listMapper
      ->add('id')
      ->add('slug')
      ->addIdentifier('laveled_title', null, array('label' => 'Категория'))
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
          'delete' => []
        ]]);
  }
  /**
   * @param FormMapper $formMapper
   */
  public function configureFormFields(FormMapper $form)
  {
    $entity = $this->getSubject();

    $form
      ->tab('Категория')
      ->add('name')
      ->add('slug')
      ->add('presentationId', ChoiceType::class, [
        'choices' => [
          'Товары' => TaxonPresentationInterface::TAXON_PRESENTATION_PRODUCTS,
          'Подкатегории' => TaxonPresentationInterface::TAXON_PRESENTATION_CHILD_SECTIONS,
        ],
        'label' => 'Вид отображения',
      ])
      ->add('description', TinyMceType::class)
      ->add('shortName', null, [
        'label' => 'Сокращённое имя'
      ])
      ->add('linkedTaxons', null, [
        'label' => 'Сопутствующие разделы',
      ])
      ->add('alfaBankTaxon', null, [
        'label' => 'Категория для Альфа-Банка'
      ])
    ->end()
    ->end()
      ->tab('Фото')
      ->add('teaser', ImageType::class, array(
        'required' => false
      ))
      ->end()
      ->end()
      ;

  }

}

