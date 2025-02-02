<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Form;

use Accurateweb\MediaBundle\Model\Image\ImageAwareInterface;
use Accurateweb\MediaBundle\Model\Media\ImageInterface;
use Accurateweb\MediaBundle\Model\Media\MediaManager;
use Accurateweb\MediaBundle\Model\Media\Storage\MediaStorageInterface;
use StoreBundle\Media\Text\NewsImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ImageType extends AbstractType
{
  private $mediaManager;

  public function __construct(MediaManager $mediaManager)
  {
    $this->mediaManager = $mediaManager;
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $entity = $form->getParent()->getData();

    if (!$entity instanceof ImageAwareInterface)
    {
      throw new \Exception();
    }

    $image = $entity->getImage($options['image_id']);

    $imageUrl = null;
    if ($image)
    {
      $storage = $this->mediaManager->getMediaStorage($image);

      $rs = $storage->retrieve($image);

      $imageUrl = $rs ? $rs->getUrl() : null;
    }

    $view->vars = array_replace($view->vars, array(
      'type' => 'file',
      'value' => '',
      'url' => $imageUrl
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function finishView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['multipart'] = true;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'compound' => false,
      //'data_class' => 'Symfony\Component\HttpFoundation\File\File',
      'empty_data' => null,
      'image_id' => null
    ));
  }

  public function getBlockPrefix()
  {
    return 'aw_media_image';
  }
}