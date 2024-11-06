<?php

namespace StoreBundle\Admin\Text;


use Accurateweb\MediaBundle\Form\ImageType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use StoreBundle\Entity\Text\Partner;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

class PartnerAdmin extends AbstractAdmin
{
  public function configureListFields(ListMapper $list)
  {
    $list
      ->add('name')
      ->add('_action', null, array(
          'actions' => array(
            'edit' => null,
            'move' => array(
              'template' => 'PixSortableBehaviorBundle:Default:_sort_drag_drop.html.twig'
            ),
            'delete' => null
          )
        )
      );
  }
  
  public function configureFormFields(FormMapper $form)
  {
    $subject = $this->getSubject();
    
    $form
      ->add('name', null, [
        'required' => true,
      ])
      ->add('teaser_image_file', ImageType::class, [
        'required' => !!$this->getSubject()->getTeaserImageFile(),
        'image_id' => 'partner/teaser'
      ]);
    
/*    $form->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT,
      function (\Symfony\Component\Form\FormEvent $event) use ($subject)
      {
        if ($this->getSubject()->getTeaser() === null)
        {
          $text = 'Нельзя создать партнёра не прикрепив изображение';
          $error = new FormError($text);
          $event->getForm()->get('teaser')->addError($error);
        }
      });*/
  }
  
  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
  }
  
  public function postPersist($object)
  {
   # $this->changeColor();
  }
  
  public function postUpdate($object)
  {
   # $this->changeColor();
  }
  
  private function changeColor()
  {
    /** @var Partner $subject */
    $subject = $this->getSubject();
    $pathPrefix = $this->getConfigurationPool()->getContainer()->getParameter('kernel.root_dir') .
      DIRECTORY_SEPARATOR . '..' .
      DIRECTORY_SEPARATOR . 'web' ;
      #DIRECTORY_SEPARATOR . 'uploads' .
    #  DIRECTORY_SEPARATOR;
    $imgPath = $pathPrefix . $subject->getThumbnailUrl('view');
    
    $imageInfo = pathinfo($imgPath);
    $ext = $imageInfo['extension'];
    $funcOpen = null;
    $funcSave = null;
    
    switch ($ext)
    {
      case 'png':
        $funcOpen = 'imagecreatefrompng';
        $funcSave = 'imagepng';
        break;
      case 'jpg':
        $funcOpen = 'imagecreatefromjpeg';
        $funcSave = 'imagejpeg';
        break;
      case 'jpeg':
        $funcOpen = 'imagecreatefromjpeg';
        $funcSave = 'imagejpeg';
        break;
    }
    
    if(null === $funcOpen || null === $funcSave)
    {
      $this->getRequest()->getSession()->getFlashBag()->add("success", "Не удалось обработать изображение. Загрузите jpg или png.");
      return;
    }
    
    $im = $funcOpen($imgPath);
    imagefilter($im, IMG_FILTER_COLORIZE,  255, 207, 64);
    $funcSave($im, $imgPath);
  }
}