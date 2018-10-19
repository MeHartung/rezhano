<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 31.10.17
 * Time: 17:28
 */

namespace StoreBundle\Form\Admin\Status;


use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Store\Order\Status\OrderStatus;
use StoreBundle\Entity\Store\Order\Status\OrderStatusReason;
use StoreBundle\Form\TinyMceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


class StatusAdminType extends AbstractType
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('status', 'choice', array(
        'by_reference' => false,
        'choices' => $this->getStatusChoices($options),
        'data' => $this->getDefault($options, 'statusId'),
        'required' => true,
        'constraints' => [new NotBlank()]
      ))
      ->add('notification', 'choice',
        [
          'by_reference' => false,
          'label' => 'Оповестить клиента?',
          'choices' => [
            'Нет' => false,
            'Да' => true,
          ],
        ])
      ->add('reasonChoice', 'choice',
        [
          'by_reference' => false,
          'label' => 'Готовые примечания',
          'choices' => $this->getReasonChoices(),
          'required' => false,
          'data' => null
        ])
      ->add('reason', TextareaType::class, $this->getReasonParameters($options));


  }

  private function getStatusChoices($data)
  {
    $repo = $this->em->getRepository(OrderStatus::class);

    if($data['data']['active'] )
    {
      $_choices = $repo->findAll();
      $choices = [];

      if($_choices)
      {
        foreach ($_choices as $_choice)
        {
          $choices[$_choice->getName()] = $_choice->getId();
        }
      }
    }
    else
    {
      $choices = $repo->getStatusChoices(false);
    }


    return $choices;
  }

  private function getDefault($data, $value)
  {
    return is_null($data['data'][$value]) ? null : $data['data'][$value];
  }

  private function getReasonChoices()
  {
    $choices = [];

    $_choices = $this->em->getRepository(OrderStatusReason::class)->findAll();

    foreach ($_choices as $_choice)
    {
      $choices[$_choice->__toString()] = $_choice->getText();
    }

    return $choices;
  }

  private function getReasonParameters($options)
  {
    $req = $options['data']['active'] ? false : true;
    return [
      'required' => $req,
      'label' => 'Примечание',
      'data' => $options['data']['reason'],
      #свойства textarea
      'attr' => [
          'style' => 'width:100%',
          'rows' => '4',
        ]
      ];
  }
}