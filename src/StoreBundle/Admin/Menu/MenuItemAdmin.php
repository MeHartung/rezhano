<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 02.08.17
 * Time: 16:05
 */

namespace StoreBundle\Admin\Menu;

use RedCode\TreeBundle\Admin\AbstractTreeAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MenuItemAdmin extends AbstractTreeAdmin
{
  protected $translationDomain = 'messages';

  protected $listModes = [
    'tree' => array(
      'class' => 'fa fa-list fa-fw',
    ),
  ];
  public function configureListFields(ListMapper $list)
  {
  }

  public function configureFormFields(FormMapper $form)
  {
    $form
      ->add('name')
      ->add('url')
      ->add('isHeaderDisplay')
      ->add('isFooterDisplay')
    ;
  }

}