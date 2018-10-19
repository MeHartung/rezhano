<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 12.09.17
 * Time: 14:49
 */

namespace StoreBundle\Form\Catalog\Product;


use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Catalog\Taxonomy\Taxon;
use StoreBundle\Form\DataTransformer\IdsToModelTransformer;
use StoreBundle\Jstree\CatalogSectionTreeListItemClientModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTaxonType extends AbstractType
{
  private $em;

  private $transformer;

  public function __construct(EntityManagerInterface $em, IdsToModelTransformer $transformer)
  {
    $this->em = $em;
    $this->transformer = $transformer;
  }
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setDefaults(array(
        'compound' => false,
      ))
      ->setRequired('product_taxons')
    ;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->addModelTransformer($this->transformer);
    /*$builder
      ->add('taxons', EntityType::class, ['class' => Taxon::class]);*/
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $taxons = $options['product_taxons'];

    $view->vars['jstree'] = $this->getJsTree($taxons);

  }

  public function getJsTree($value, $attributes = array(), $errors = array())
  {
    $em = $this->em;

    $array = [];

    $name = '';

    $id = 'product_taxons';

    if (null !== $value)
    {
      if (!is_array($value))
      {
        $values = array($value);
      }
      else
      {
        $values = $value;
      }
    }
    else
    {
      $values = array();
    }

    $rootSection =$em
      ->getRepository('StoreBundle:Store\Catalog\Taxonomy\Taxon')
      ->getRootNode()
    ;

    $rootClientModel = new CatalogSectionTreeListItemClientModel($rootSection);

    $jsTreeData = $this->selectNodes(array($rootClientModel->getClientModelValues()), $values);
    $jsTreeData = $jsTreeData[0];

    $array = [
      'id' =>$id,
      'data' => json_encode($jsTreeData),
      'multiple' => (bool)true
        ];

    return $array;
  }

  public function selectNodes($jsTreeData, $nodeIds)
  {
    foreach ($jsTreeData as $idx => $node)
    {
      if (isset($node['metadata']['id']) && in_array((string)$node['metadata']['id'], $nodeIds))
      {
        if (!isset($jsTreeData[$idx]['attr']))
        {
          $jsTreeData[$idx]['attr'] = array();
        }
        $jsTreeData[$idx]['attr']['class'] = 'jstree-checked';
      }
      if (isset($node['children']))
      {
        $jsTreeData[$idx]['children'] = $this->selectNodes($node['children'], $nodeIds);
      }
    }

    return $jsTreeData;
  }

  public function getBlockPrefix()
  {
    return 'taxon_jstree';
  }


}