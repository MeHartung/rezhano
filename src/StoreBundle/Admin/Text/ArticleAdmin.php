<?php

/*
 *  @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */

namespace StoreBundle\Admin\Text;

use Accurateweb\MediaBundle\Form\ImageType;
use StoreBundle\Entity\Text\Article;

//use MainBundle\Form\LabelType;
//use MainBundle\Interfaces\Uploadable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use StoreBundle\Form\TinyMceType;

/**
 *
 *
 * @author Denis N. Ragozin <dragozin at accurateweb.ru>
 */
class ArticleAdmin extends AbstractAdmin
{
  // protected $classnameLabel = 'Статьи';
  const CROP_THUMB_MAX_WIDTH = 150;
  const CROP_THUMB_MAX_HEIGHT = 100;
  protected $translationDomain = 'messages';

  public function toString($object)
  {
    if ($object instanceof Article)
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

  /**
   * @param DatagridMapper $datagridMapper
   */
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
      ->add('title');
  }

  /**
   * @param ListMapper $listMapper
   */
  protected function configureListFields(ListMapper $listMapper)
  {

    $listMapper
      ->add('title')
      ->add('published')
      ->add('slug')
      ->add('createdAt', 'datetime', [
        'format' => 'd.m.Y H:i',
      ])
      ->add('updatedAt', 'datetime', [
        'format' => 'd.m.Y H:i',
      ])
      ->add('_action', null, array(
        'actions' => array(
          'show' => array(),
          'edit' => array(),
          'delete' => array(),
        ),
        ));
  }

  /**
   * @param FormMapper $formMapper
   */
  protected function configureFormFields(FormMapper $formMapper)
  {
    $entity = $this->getSubject();

    $formMapper
      ->tab('Текст')
      ->add('title')
      ->add('slug', null, array(
        'required' => false,
        'label' => 'Алиас'))
      ->add('announce', TinyMceType::class, array(
        'required' => false,
        'label' => 'Анонс *'))
      ->add('text', TinyMceType::class, array(
        'required' => false,
        'label' => 'Текст *',

      )/*, array(
        'source_field' => 'text',
        'target_field' => 'text',
        'format_field' => 'textFormatter',
        'source_field_options' => array(
          'attr' => array('class' => 'span20', 'rows' => 20)
        ),
        'ckeditor_context' => 'default',
        'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
        'listener' => true,
      )*/)
      ->end()
      ->end()
      ->tab('Фото')
      ->add('teaserImageFile', ImageType::class, array(
        'required' => false
      ))
//      ->add('file', 'file', $this->getFileFieldOptions())
      //НЕ РАБОТАЕТ В IE9
//          ->add('image', 'comur_image', array(
//            'required' => false,
//            'uploadConfig' => array(
//                'uploadRoute' => 'comur_api_upload',        //optional
//                'uploadUrl' => $entity->getUploadRootDir(),       // required 
//                'webDir' => $entity->getUploadDir(),              // required 
//                'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',    //optional
//                'libraryDir' => null,                       //optional
//                'libraryRoute' => 'comur_api_image_library', //optional
//                'showLibrary' => true,                      //optional
//                'saveOriginal' => 'originalImage',          //optional
//                'generateFilename' => true          //optional
//            ),
//            'cropConfig' => array(
//                'minWidth' => 150,
//                'minHeight' => 100,
//                'aspectRatio' => true,              //optional
//                'cropRoute' => 'comur_api_crop',    //optional
//                'forceResize' => false,             //optional
//                'thumbs' => array(                  //optional
//                    array(
//                        'maxWidth' => self::CROP_THUMB_MAX_WIDTH,
//                        'maxHeight' => self::CROP_THUMB_MAX_HEIGHT,
//                        'useAsFieldImage' => true  //optional
//                    )
//                )
//            )
//          ))
      ->end()
      ->end()
      ->tab('Свойства')
      ->add('published')
//      ->add('gallery')
      ->end()
      ->end()
//      ->tab('Метки')
//      ->add('labels', 'collection', [
//        'mapped' => true,
//        'required' => false,
//        'type' => new LabelType(),
//        'allow_add' => true,
//        'allow_delete' => true,
//      ],
//        [
//          'edit' => 'inline',
//          'inline' => 'table',
//          'sortable' => 'position',
//        ]
//      )
//      ->end()
//      ->end();
    ;
  }

//  private function getFileFieldOptions()
//  {
//    $object = $this->getSubject();
//    $router = $this->getConfigurationPool()->getContainer()->get('router');
//
//    $fileFieldOptions = [
//      'label' => 'Изображение',
//      'required' => false,
//      'data_class' => null
//    ];
//
//    $fileInfo = $object->getFile();
//    if ($object instanceof Uploadable && isset($fileInfo['url']))
//    {
//
//      $fileFieldOptions['help'] = '<img src="' . $fileInfo['url'] . '" class="admin-preview" style="max-width: 500px;" />';
//
//      if ($object->getId())
//      {
//        $deleteDescriptionImageURL = $router->generate('news_image_delete', [
//          'id' => $object->getId()
//        ]);
//        $fileFieldOptions['help'] .= '<a class="btn btn-danger" style="margin-left: 10px" href="' . $deleteDescriptionImageURL . '">Удалить изображение</a>';
//      }
//    }
//
//    return $fileFieldOptions;
//  }

  /**
   * @param ShowMapper $showMapper
   */
  protected function configureShowFields(ShowMapper $showMapper)
  {
    $showMapper
      ->add('title')
      ->add('announce')
      ->add('text');
  }


}