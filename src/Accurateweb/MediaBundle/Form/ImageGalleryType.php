<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace Accurateweb\MediaBundle\Form;

use Accurateweb\MediaBundle\Model\Media\MediaManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

class ImageGalleryType extends AbstractType
{
  private $router;

  private $mediaManager;

  public function __construct(Router $router, MediaManager $mediaManager)
  {
    $this->router = $router;
    $this->mediaManager = $mediaManager;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent::buildForm($builder, $options);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setDefault('mapped', false)
      ->setRequired('gallery')
      ->setDefault('crop', false)

      ->setAllowedValues('mapped', array(false))
    ;
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $entity = $form->getParent()->getData();

    $galleryId = $entity->getId();

    $view->vars = array_replace($view->vars, array(
      'image_list_url' => $this->router->generate('accurateweb_media_bundle_image_gallery_editor_list', array(
        'gallery_provider_id' => $options['gallery'],
        'gallery_id' => $galleryId
      )),
      'image_upload_url' => $this->router->generate('accurateweb_media_bundle_gallery_upload', array(
        'gallery_provider_id' => $options['gallery'],
        'gallery_id' => $galleryId
      )),
      'image_delete_url' => $this->router->generate('accurateweb_media_bundle_image_gallery_editor_delete', array(
        'gallery_provider_id' => $options['gallery'],
        'gallery_id' => $galleryId
      )),
      'crop' => $this->getCropOptions($options),
      'js_options' => array()
    ));
  }

  public function getBlockPrefix()
  {
    return "aw_media_gallery";
  }

  protected function getCropOptions($options)
  {
    $cropOptions = $options['crop'];

    $crop = null;

    if (false !== $cropOptions)
    {
      $crop = array();

      if (!isset($options['size']))
        throw new InvalidArgumentException('Crop option requires size parameter to be set');

      if (isset($options['boxwidth']))
        $crop['boxWidth'] = $options['boxwidth'];
      if (isset($options['boxheight']))
        $crop['boxHeight'] = $options['boxheight'];

      $size = $options['size'];
      if (is_string($size))
        $size = explode('x', $size);

      $crop['aspectRatio'] = $size[1] > 0 ? round($size[0] / $size[1], 2) : 1;
      $crop['minSize'] = $size;
    }

    return $crop;
  }
}