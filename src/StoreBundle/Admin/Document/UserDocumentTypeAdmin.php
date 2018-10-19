<?php

namespace StoreBundle\Admin\Document;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use StoreBundle\Entity\Document\UserDocumentType;

class UserDocumentTypeAdmin extends AbstractAdmin
{
  protected $translationDomain = 'messages';

  protected $datagridValues = array(
    '_page' => 1,
    '_sort_order' => 'ASC',
    '_sort_by' => 'position',
  );

  private $currentPositionField;

  public function getFilterParameters ()
  {
    $filterParams = parent::getFilterParameters();

    $field = null;

    if (isset($filterParams['showIndividual']) && isset($filterParams['showJuridical']) && isset($filterParams['showIndividual']))
    {
      $showIndividual = $filterParams['showIndividual']['value']
        && !$filterParams['showJuridical']['value']
        && !$filterParams['showEnterpreneur']['value'];
      $showJuridical = !$filterParams['showIndividual']['value']
        && $filterParams['showJuridical']['value']
        && !$filterParams['showEnterpreneur']['value'];
      $showEnterpreneur = !$filterParams['showIndividual']['value']
        && !$filterParams['showJuridical']['value']
        && $filterParams['showEnterpreneur']['value'];

      if ($showIndividual)
      {
        $field = 'positionIndividual';
      }
      elseif ($showJuridical)
      {
        $field = 'positionJuridical';
      }
      elseif ($showEnterpreneur)
      {
        $field = 'positionEnterpreneur';
      }
    }

    $this->currentPositionField = $field;

    if ($field)
    {
      $filterParams['_sort_by'] = $field;
    }

    return $filterParams;
  }

  protected function configureFormFields (FormMapper $form)
  {
    $subject = $this->getSubject();
    $fileOptions = [
      'data' => null,
      'label' => 'Бланк',
    ];

    if ($subject->getFile())
    {
      $fileOptions['help'] = $subject->getFile();
    }

    $form
      ->add('name')
      ->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType', $fileOptions)
      ->add('showIndividual')
      ->add('showJuridical')
      ->add('showEnterpreneur');
  }

  protected function configureListFields (ListMapper $list)
  {
    $showIndividual = $this->getPositionField() === 'positionIndividual';
    $showJuridical = $this->getPositionField() === 'positionJuridical';
    $showEnterpreneur = $this->getPositionField() === 'positionEnterpreneur';

    $actionButtons = [
      'actions' => [
        'edit' => [],
        'delete' => [],
      ]
    ];

    if ($showEnterpreneur || $showIndividual || $showJuridical)
    {
      $actionButtons['actions']['move'] = [
        'template' => '@Store/Admin/Document/_sort_drag_drop.html.twig',
        'enable_top_bottom_buttons' => false,
      ];
    }

    $list
      ->add('name')
      ->add('showIndividual')
      ->add('showJuridical')
      ->add('showEnterpreneur')
      ->add('createdAt', 'date', [
        'format' => 'd.m.Y',
      ])
      ->add('_action', null, $actionButtons);
  }

  protected function configureDatagridFilters (DatagridMapper $filter)
  {
    $filter
//      ->add('positionField', 'doctrine_orm_choice', ['label' => 'Type',
//        'field_options' => [
//          'required' => false,
//          'mapped' => false,
//          'choices' => [
//            "showIndividual" => "showIndividual",
//            "showJuridical" => "showJuridical",
//            "showEnterpreneur" => "showEnterpreneur"
//          ]
//        ],
//        'field_type' => 'choice',
//      ])
      ->add('showIndividual')
      ->add('showJuridical')
      ->add('showEnterpreneur');
  }

  protected function configureRoutes (RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
  }

  public function configure ()
  {
    $this->setTemplate('list', 'StoreBundle:SonataAdmin\CRUD:list_sortable.html.twig');
  }

  /**
   * @return string|null
   */
  public function getPositionField ()
  {
    $field = $this->currentPositionField;

    if (!$field && $this->getRequest()->get('field'))
    {
      $field = $this->getRequest()->get('field');
    }

    return $field;
  }

  /**
   * @param UserDocumentType $subject
   * @return integer|null
   */
  public function getCurrentObjectPosition($subject)
  {
    $field = $this->getPositionField();

    switch ($field)
    {
      case 'positionIndividual':
        return $subject->getPositionIndividual();
      case 'positionJuridical':
        return $subject->getPositionJuridical();
      case 'positionEnterpreneur':
        return $subject->getPositionEnterpreneur();
    }

    return null;
  }

  /**
   * @param UserDocumentType $subject
   * @return integer|null
   */
  public function getLastPosition($subject)
  {
    $field = $this->getPositionField();
    $query = $this->getModelManager()->createQuery($this->getClass());
    $query
      ->select(sprintf('MAX(o.%s) AS position', $field))
      ->where('o.showJuridical = TRUE');

    switch ($field)
    {
      case 'positionIndividual':
        $query->where('o.showIndividual = TRUE');
        break;
      case 'positionJuridical':
        $query->where('o.showJuridical = TRUE');
        break;
      case 'positionEnterpreneur':
        $query->where('o.showEnterpreneur = TRUE');
        break;
    }

    return $query->getSingleScalarResult();
  }
}