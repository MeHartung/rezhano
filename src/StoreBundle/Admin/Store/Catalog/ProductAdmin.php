<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Admin\Store\Catalog;

use Accurateweb\MediaBundle\Form\ImageGalleryType;
use Accurateweb\MediaBundle\Form\ImageType;
use Sonata\CoreBundle\Validator\ErrorElement;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Form\Catalog\Product\ProductTaxonType;
use StoreBundle\Form\DataTransformer\NullableBooleanToBooleanTransformer;
use StoreBundle\Form\ProductAttributeValueToProductType;
use StoreBundle\Form\TinyMceType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotNull;

class ProductAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';

  protected function configureListFields(ListMapper $list)
  {
    /**
     *  'editable' => true будет работать только если тип text/choice
     */
    $list
      ->add('sku')
      ->add('name')
      ->add('productType', null)
      ->add('brand')
      ->add('price', 'text', [
        'editable' => true,
      ])
      ->add('oldPrice', 'text', [
        'editable' => true,
      ])
      ->add('wholesalePrice', 'text', [
        'editable' => true,
      ])
//      ->add('hit')
//      ->add('sale')
//      ->add('novice')
//      ->add('isPurchasable')
      ->add('publicationAllowed')
      ->add('published')
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
          'copyProduct' => [
            'template' => 'StoreBundle:CRUD/Store/Product:list__action_copy.html.twig'
          ],
          'delete' => [],
        ]
      ]);

  }

  protected function configureFormFields(FormMapper $form)
  {

    $taxonsArray = [];
    $taxonsCollection = $this->getSubject()->getTaxons();

    foreach ($taxonsCollection as $taxon)
    {
      $taxonsArray[] = $taxon->getId();
    }

    $form
      ->tab('Основные')
      ->add('sku', TextType::class)
      ->add('name', TextType::class)
      ->add('slug', TextType::class, [
        'help' => 'Если оставить пустым, то будет сгенерирован автоматически.'
      ])
      ->add('productType')
      ->add('brand')
      ->add('price', NumberType::class, [
        'required' => true,
        'constraints' => [
          new NotNull(['message' => 'Вы должны указать цену товара']),
        ]
      ])
      ->add('oldPrice', NumberType::class)
      ->add('wholesalePrice', NumberType::class)
      ->add('purchasePrice', NumberType::class)
      ->add('publicationAllowed', BooleanType::class, array(
        'transform' => true
      ))
      ->add('units')
      ->add('package', NumberType::class, ['required' => false])
      ->add('unitWeight', NumberType::class, ['required' => false])
      ->add('multiplier', ChoiceType::class,
        [
          'required' => true,
          'choices' => [
            1 => 1,
            1000 => 1000,
          ],
          'help' => 'Выберите 1000, если вес в гр., 1 если в кг'
        ])
//      ->add('isPurchasable', BooleanType::class, array(
//        'transform' => true,
//        'help' => 'Если нет, товар будет отображен, как «Снят с производства»'
//      ))
//      ->add('length')
//      ->add('width')
//      ->add('weight')
//      ->add('volume')
      ->end()
      ->end()
      ->tab('Описание')
      ->add('shortDescription', TextType::class, array(
        'empty_data' => '',
      ))
      ->add('description', TinyMceType::class)
      ->end()
      ->end()
    ;

    if ($this->getSubject()->getId())
    {
      $form
        ->tab('Разделы каталога')
        ->add('taxons', ProductTaxonType::class,
          [
            'label' => 'Разделы каталога',
            'product_taxons' => $taxonsArray,
           // 'by_reference' => false
          ])
        ->end()
        ->end();


    }

    if ($this->getSubject()->getProductType() !== null)
    {
      $form
        ->tab('Свойства')
        ->add('productAttributeValues', ProductAttributeValueToProductType::class, [
          'label' => 'Свойства',
          'productType' => $this->getSubject()->getProductType(),
        ])
        ->end()
        ->end();
    }

    $form
      ->tab('Промо')
      ->add('hit', BooleanType::class, array(
        'transform' => true
      ))
      ->add('sale', BooleanType::class, array(
        'transform' => true
      ))
      ->add('novice', ChoiceType::class, array(
        'choices' => array(
          'автоматически' => '',
          'да' => 1,
          'нет' => 0
        )
      ))
      ->end()
      ->end();
    
    if ($this->getSubject()->getId())
    {
      $form
        ->tab('Фотогалерея')
 /*         ->add('background', ChoiceType::class, [
            'choices' => [
              'Оранжевая' => Product::ORANGE_BACKGROUND,
              'Черная' => Product::BLACK_BACKGROUND,
            ],
            'required' => true,
            #'help' => 'Выберите цвет подложки для фото товара.'
          ])*/
        ->add('teaserImageFile', ImageType::class, array(
          'required' => false,
          'label' => 'Главное фото товара',
          'help' => 'Отображается в списке товаров.'
        ))
          ->add('productPhotos', ImageGalleryType::class, array(
            'gallery' => 'product-photo',
            'label' => false,
            'crop' => [
              'size' => '570x713', //Размеры поля кропа (отношение сторон будет сохранено)
              'boxWidth' => 800, //Размеры модального окна
              'boxHeight' => 600
            ]
          ))
        ->end()
        ->end()
/*        ->tab('Сопутствующие товары')
          ->add('relatedProducts')
        ->end()
        ->end()*/;
    }
    ;

    $form->get('novice')->addModelTransformer(new NullableBooleanToBooleanTransformer());
  }

  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('clone', $this->getRouterIdParameter().'/clone');
  }

  protected function configureDatagridFilters(DatagridMapper $filter)
  {
    $filter->add('sku')
           ->add('brand')
           ->add('name')
           ->add('slug')
           ->add('productType')
           ->add('taxons')
           ->add('publicationAllowed')
            ;
  }

  /*
   * Вызывается flush, для того, чтобы сохранились категории, которые были изменены в
   *   StoreBundle\EventListener\TaxonNbProductsAggregate::postFlush, т.к. там они не сохраняются
   */
  public function postUpdate ($object)
  {
    $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')->flush();
  }
  
  public function validate(ErrorElement $errorElement, $object)
  {
    $errorElement
    ->with('shortDescription')
      ->assertLength(array('max' => 32))
      #->addViolation('Не более 32 символов!')
      ->end()
    ;
  }
}