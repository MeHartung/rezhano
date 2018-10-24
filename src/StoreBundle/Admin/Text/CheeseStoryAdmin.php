<?php


namespace StoreBundle\Admin\Text;


use Accurateweb\MediaBundle\Form\ImageType;
use StoreBundle\Entity\Text\CheeseStory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use StoreBundle\Form\TinyMceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;

class CheeseStoryAdmin extends AbstractAdmin
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
    $text = substr($this->getSubject()->getText(), 0, 50);
    $list
      ->add('text', null, ['data' => $text])
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
    /** @var CheeseStory $subject */
    $subject = $this->getSubject();
    $form
      #->add('title')
      ->add('text', TinyMceType::class)
    #  ->add('teaser', ImageType::class, array(
    #    'required' => $subject->getTeaser() === null
    #  ));
  ;
   # $form->getFormBuilder()->addEventListener(FormEvents::POST_SUBMIT,
   #   function (\Symfony\Component\Form\FormEvent $event) use ($subject)
   #   {
   #     if ($subject->getTeaser() === null)
   #     {
   #       $nullPhotoError = new FormError("У истории должно быть фото!");
   #       $event->getForm()->get('teaser')->addError($nullPhotoError);
   #     }
   #   });
  }
  
  
  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
  }
}