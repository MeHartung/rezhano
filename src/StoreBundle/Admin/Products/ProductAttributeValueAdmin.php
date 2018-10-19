<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 09.08.17
 * Time: 15:14
 */

namespace StoreBundle\Admin\Products;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductAttributeValueAdmin extends AbstractAdmin
{

  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('value')
      ->add('_action', null, [
        'actions' => [
          'edit' => [],
          'delete' => []
        ]
      ]);
    ;
  }

  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->add('value', TextType::class, array(
        'required' => true,
        'constraints' => array(
          new NotBlank()
        )
      ))
    ;
  }

 /* public function prePersist($project)
  {
    $this->preUpdate($project);

  }

  public function preUpdate($project)
  {
    $project->setProductAttribute($project->getProductAttribute());
  }*/
}