<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 04.08.17
 * Time: 11:38
 */

namespace StoreBundle\Admin\Text;

use Accurateweb\MediaBundle\Form\ImageType;
use StoreBundle\Entity\Text\SpecialOffer;
use RedCode\TreeBundle\Admin\AbstractTreeAdmin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use StoreBundle\Form\TinyMceType;

class SpecialOfferAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';

  public function toString($object)
  {
    if ($object instanceof SpecialOffer)
    {
      if (mb_strlen($object->getTitle(), 'UTF-8') > 35)
      {
        $s = mb_substr($object->getTitle(), 0, 32, 'UTF-8') . '...';
      } else
      {
        $s = $object->getTitle();
      }
    }
    return $s;
  }

  public function configureListFields(ListMapper $list)
  {
   $list
     ->add('id')
     ->add('title')
     ->add('slug')
     ->add('dateStart','datetime', [
       'format' => 'd.m.Y H:i',
        ])
     ->add('dateEnd','datetime', [
       'format' => 'd.m.Y H:i',
        ])
     ->add('_action', null, [
       'actions' => [
         'show' => [],
         'edit' => [],
         'delete' => [],
       ]
     ]
  );

  }

  public function configureFormFields(FormMapper $form)
  {
   $form
     ->tab('Основные')
       ->add('title')
       ->add('slug')
       ->add('dateStart', 'sonata_type_date_picker')
       ->add('dateEnd', 'sonata_type_date_picker')
       ->add('announce', TinyMceType::class)
       ->add('text', TinyMceType::class)
       ->end()
     ->end()
     ->tab('Фото')
      ->add('teaserImageFile', ImageType::class, [
        'required' => false
      ])
     ->end()
     ->end()
     ;
  }
}