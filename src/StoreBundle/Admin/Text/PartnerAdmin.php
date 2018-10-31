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
      ->add('name')
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
    $this->changeColor();
  }
  
  public function postUpdate($object)
  {
    $this->changeColor();
  }
  
  private function changeColor()
  {
    /** @var Partner $subject */
    $subject = $this->getSubject();
    $pathPrefix = $this->getConfigurationPool()->getContainer()->getParameter('kernel.root_dir') .
      DIRECTORY_SEPARATOR . '..' .
      DIRECTORY_SEPARATOR . 'web' .
      DIRECTORY_SEPARATOR . 'uploads' .
      DIRECTORY_SEPARATOR;
    
    $imgPath = $pathPrefix . $subject->getTeaserImageFile();
    
    $imageInfo = pathinfo($imgPath);
    $ext = $imageInfo['extension'];
    $funcOpen = null;
    
    switch ($ext)
    {
      case 'png':
        $funcOpen = 'imagecreatefrompng';
        $funcSave = 'imagepng';
        break;
      case 'jpg':
        $funcOpen = 'imagecreatefromjpeg';
        break;
      case 'jpeg':
        $funcOpen = 'imagecreatefromjpeg';
        break;
    }
    
    $im = imagecreatefrompng($imgPath);
    
    $myRed = 255;
    $myGreen = 207;
    $myBlue = 64;
    $myAlpha = 0;
    
    imageAlphaBlending($im, true);
    imageSaveAlpha($im, true);
    
    if (imageistruecolor($im))
    {
      $sx = imagesx($im);
      $sy = imagesy($im);
      
      for ($x = 0;$x < $sx;$x++)
      {
        for ($y = 0;$y < $sy;$y++)
        {
          $c = imagecolorat($im, $x, $y);
          $colors = imagecolorsforindex($im, $c);
  /*        if ($colors['red'] == 255 && $colors['green'] == 255 && $colors['blue'] == 255)
          {
            var_dump($c);die;
          }*/
          $a = $c & 0xFF000000;
          if ($colors['red'] !== 255 && $colors['green'] !== 255 && $colors['blue'] !== 255)
          {
            #$newColor = $a | $myRed << 16 | $myGreen << 8 | $myBlue;
            $newColor = imagecolorallocate($im, 255, 207, 64);
            imagesetpixel($im, $x, $y, $newColor);
          }
        
        }
      }
    } else
    {
      $numColors = imagecolorstotal($im);
      $transparent = imagecolortransparent($im);
      
      for ($i = 0;$i < $numColors;$i++)
      {
        if ($i != $transparent)
          imagecolorset($im, $i, $myRed, $myGreen, $myBlue, $myAlpha);
        
      }
    }
    
    imagepng($im, $imgPath);
    /*    $size = getimagesize($pathPrefix . $this->getSubject()->getTeaser());
        
        if (is_bool($size)) throw new FileNotFoundException("File $imgPath not found!");
        
        list($w, $h) = call_user_func_array(function ($size)
        {
          return array_splice($size, 0, 2);
        }, [$size]);
        
        $x = $y = 0;
        
        while ($x < $w)
        {
          while ($y < $h)
          {
            $rgb = imagecolorat($image, $x, $y);
            
            $colors = imagecolorsforindex($image, $rgb);
            if ($colors['red'] !== 255 && $colors['green'] !== 255 && $colors['blue'] !== 255)
            {
              $needleColor = imagecolorallocate($image, 255, 207, 64);
              imagesetpixel($image, $x, $y, $needleColor);
            }
            
            $y++;
          }
          $x++;
          $y = 0;
        }
        
        imagejpeg($image, $imgPath);*/
    
  }
}