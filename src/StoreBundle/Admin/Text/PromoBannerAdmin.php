<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Admin\Text;

use Accurateweb\MediaBundle\Form\ImageType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PromoBannerAdmin extends AbstractAdmin
{
  protected $datagridValues = array(
    '_page' => 1,
    '_sort_order' => 'ASC',
    '_sort_by' => 'position',
  );

  public function configure()
  {
    $this->setTemplate('list', 'StoreBundle:SonataAdmin\CRUD:list_sortable.html.twig');
  }

  protected function configureListFields(ListMapper $list)
  {
    #$text = substr($this->getSubject()->getText(), 0, 50);
    $list
      ->add('__toString', null, ['label' => 'Текст'])
      ->add('published')
      #->add('title', null)
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
  protected function configureFormFields(FormMapper $form)
  {
    $form
      ->add('teaser_image_file', ImageType::class, [
        'required' => !!$this->getSubject()->getTeaserImageFile(),
        'image_id' => 'homepage-promo-banner/teaser'
      ])
      ->add('text_image_file', ImageType::class, [
        'required' => !!$this->getSubject()->getTeaserImageFile(),
        'image_id' => 'homepage-promo-banner/text'
      ])
      ->add('text', TextareaType::class, [
        'required' => true
      ])
      ->add('buttonText', null, [
        'required' => true
      ])
      ->add('url', null, [
        'required' => true
      ])
      ->add('published')
    ;
  }

  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
  }
}