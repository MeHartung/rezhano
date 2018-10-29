<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Admin\Store\Payment\Method;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PaymentMethodAdmin extends AbstractAdmin
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

  protected function configureFormFields(FormMapper $form)
  {
    $form->add('name')
         ->add('description')
         ->add('info', null, [
           'label' => 'Информация',
           'help' => 'Краткая информация о доставке'
         ])
         ->add('enabled')
         ->add('availability_decision_manager_id', ChoiceType::class, [
           'choices' => array_flip($this->getAvailabilityDecisionManagerChoices())
         ])
         ->add('fee_calculator_id', ChoiceType::class, [
          'choices' => array_flip($this->getFeeCalculatorChoices())
         ])
         ->add('type', 'choice', [
           'choices' =>
           [
             'Обычный платёж' => '6cdec659-199f-43b6-ac05-f87a3a552f51',
           ]
         ])
    ;
  }

  protected function configureListFields(ListMapper $list)
  {
    $list
      ->addIdentifier('name')
      ->add('enabled', null, [
        'editable' => true
      ])
      ->add('availability_decision_manager_id', 'choice', [
        'choices' => $this->getAvailabilityDecisionManagerChoices()
      ])
      ->add('fee_calculator_id', 'choice', [
        'choices' => $this->getFeeCalculatorChoices()
      ])
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

  protected function configureRoutes(RouteCollection $collection)
  {
    $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
  }

  private function getAvailabilityDecisionManagerChoices()
  {
    $paymentMethodManager = $this->getConfigurationPool()->getContainer()->get('accuratecommerce.payment.method.manager');

    $availabilityDecisionManagerChoices = [];

    foreach ($paymentMethodManager->getAvailabilityDecisionManagers() as $availabilityDecisionManager)
    {
      $availabilityDecisionManagerChoices[$availabilityDecisionManager->getId()] = $availabilityDecisionManager->getName();
    }

    return $availabilityDecisionManagerChoices;
  }

  private function getFeeCalculatorChoices()
  {
    $feeCalculators = $this->getConfigurationPool()->getContainer()
        ->get('accuratecommerce.payment.method.fee.calculator.repository')->findAll();

    $feeCalculatorChoices = [];

    foreach ($feeCalculators as $feeCalculator)
    {
      $feeCalculatorChoices[$feeCalculator->getId()] = $feeCalculator->getName();
    }

    return $feeCalculatorChoices;
  }
}