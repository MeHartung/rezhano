<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Form\Checkout;


use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;
use StoreBundle\Entity\Store\Shipping\ShippingMethod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContext;

class CheckoutType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $data = $builder->getForm()->getData();
    $builder
      ->add('customer_phone', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите Ваш номер телефона']),
          //new Regex(['pattern' => '/\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/'])
        ]
      ])
      ->add('customer_last_name', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите Вашу фамилию']),
        ]
      ])
      ->add('customer_first_name', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите Ваше имя']),
        ]
      ])
      ->add('customer_email', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите Ваш Email']),
          new Email()
        ]
      ])
      ->add('shipping_city_name', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
        'choices' => [
          'Екатеринбург' => 'Екатеринбург',
          'Реж' => 'Реж',
          'Другой город' => ''
        ],
        'required' => true,
       # 'expanded' => true,
        'label' => 'Город доставки',
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите город доставки']),
        ],
      ])/*
      ->add('shipping_city_name', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите город доставки']),
        ]
      ])*/
      ->add('shipping_post_code', TextType::class, [
        'required' => false,
        'label' => false
      ])
      ->add('shipping_address', TextType::class, [
        'required' => false,
        'label' => false
      ])
      ->add('customer_comment', TextareaType::class, [
        'label' => false,
        'required' => false
      ])
      ->add('shipping_method_id', EntityType::class, [
        'class' => ShippingMethod::class,
        'query_builder' => function (EntityRepository $er) use ($data)
        {
          /** @var Order $data */
          $city = $data->getShippingCityName();

          if($city === 'Реж' || $city === 'Екатеринбург')
          {
            return $er->createQueryBuilder('sm')
              ->where('sm.uid != :uid')
              ->orderBy('sm.position')
              ->setParameter('uid', ShippingMethodUserDefined::UID);
          }else
          {
            return $er->createQueryBuilder('sm')
              ->where('sm.uid = :uid')
              ->orderBy('sm.position')
              ->setParameter('uid', ShippingMethodUserDefined::UID);
          }
        }
        ,
        'required' => true,
        'error_bubbling' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите способ оплаты заказа'])
        ]
      ])
      ->add('payment_method', EntityType::class, [
        'class' => PaymentMethod::class,
        'required' => true,
        'error_bubbling' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите способ оплаты заказа'])
        ]
      ])
      ->add('tos_agreement', CheckboxType::class, [
        'mapped' => false,
        'required' => true,
        'constraints' => [
          new IsTrue(['message' => 'Чтобы оформить заказ, необходимо принять Условия обслуживания'])
        ]
      ])
      ->add('submit', SubmitType::class)
      ->add('orderItems', null, [
        'required' => true,
        'inherit_data' => true,
        'constraints' => [
          new NotNull(),
          new Callback(['callback' => [$this, 'hasNotPreorderProducts']])
        ]
      ]);
  }
  
  
  /**
   * @param $order
   * @param ExecutionContext $context
   */
  public function hasNotPreorderProducts($order, ExecutionContext $context)
  {
    return;
/*    $orderItems = $order->getOrderItems();
    foreach ($orderItems as $orderItem)
    {
      if ($orderItem->getProduct()->isPreorder())
      {
        $context->addViolation(sprintf('Товар %s доступен только для предзаказа', $orderItem->getProduct()->getName()));
        return;
      }
    }*/
  }



  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Order::class
    ]);
  }
}