<?php
/**
 * @author Denis N. Ragozin <dragozin@accurateweb.ru>
 */

namespace StoreBundle\Form\Checkout;


use AccurateCommerce\Shipping\Method\ShippingMethodUserDefined;
use AccurateCommerce\Shipping\Method\Store\ShippingMethodStorePickup;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Event\FormEvent;
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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
    $formModifier = function (FormInterface $form, $city = 'Екатеринбург')
    {
      if ($city === 'Екатеринбург' || $city === 'Реж')
      {
        $qb = function (EntityRepository $er) use ($city)
        {
          return $er->createQueryBuilder('sm')
            ->where('sm.uid != :uid')
            ->orderBy('sm.position')
            ->setParameter('uid', ShippingMethodUserDefined::UID);
        };
      } else
      {
        $qb = function (EntityRepository $er) use ($city)
        {
          return $er->createQueryBuilder('sm')
            ->where('sm.uid = :uid')
            ->orderBy('sm.position')
            ->setParameter('uid', ShippingMethodUserDefined::UID);
        };
      }
  
      $form->add('shippingMethod', null, [
        'required' => true,
        'expanded' => true,
        'label' => false,
        'query_builder' => $qb
      ]);
    };
    
    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function (\Symfony\Component\Form\FormEvent $event) use ($formModifier)
      {
        /** @var Order $data */
        $data = $event->getData();
        
        $formModifier($event->getForm(), $data->getShippingCityName());
      }
    );
    
    $builder
      ->add('customerType', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
        'required' => true,
        'label' => false,
        'choices' => [
          'Частное лицо' => Order::CUSTOMER_TYPE_INDIVIDUAL,
          'Юридическое лицо' => Order::CUSTOMER_TYPE_LEGAL,
        ],
        'expanded' => true,
        'multiple' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите лицо']),
          //new Regex(['pattern' => '/\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/'])
        ],
        'block_name' => 'customer_type_block',
       /* 'choice_attr' => function($choiceValue, $key, $value) {
          // adds a class like attending_yes, attending_no, etc
          return ['class' => 'attending_'];
        },*/
      ])
      ->add('customer_phone', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите Ваш номер телефона']),
          //new Regex(['pattern' => '/\+7\s\(\d{3}\)\s\d{3}\-\d{2}\-\d{2}/'])
        ]
      ])/*
      ->add('customer_last_name', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите Вашу фамилию']),
        ]
      ])*/
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
          'Другой город' => 'Другой город'
        ],
        'required' => true,
        'label' => 'Город доставки',
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите город доставки']),
        ],
        'data' => 'Екатеринбург'
      ])/*
      ->add('shipping_city_name', TextType::class, [
        'required' => true,
        'label' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите город доставки']),
        ]
        ])*//*
        ->add('shipping_post_code', TextType::class, [
          'required' => false,
          'label' => false
        ])*/
      ->add('shipping_address', TextType::class, [
        'required' => false,
        'label' => false
      ])
      ->add('customer_comment', TextareaType::class, [
        'label' => false,
        'required' => false
      ])
      ->add('payment_method', EntityType::class, [
        'class' => PaymentMethod::class,
        'required' => true,
        'error_bubbling' => false,
        'constraints' => [
          new NotBlank(['message' => 'Пожалуйста, укажите способ оплаты заказа'])
        ],
        'expanded' => true,
        'label' => false
      ])
      ->add('tos_agreement', CheckboxType::class, [
        'mapped' => false,
        'required' => true,
        'constraints' => [
          new IsTrue(['message' => 'Чтобы оформить заказ, необходимо принять Условия обслуживания'])
        ]
      ])
/*      ->add('personalInformationAgreement', CheckboxType::class, [
        'mapped' => false,
        'required' => true,
        'constraints' => [
          new IsTrue(['message' => 'Чтобы оформить заказ, необходимо принять политику обработки персональных данных'])
        ]
      ])*/
      ->add('submit', SubmitType::class)/*->add('orderItems', null, [
        'required' => true,
        'inherit_data' => true,
        'constraints' => [
          new NotNull(),
          new Callback(['callback' => [$this, 'hasNotPreorderProducts']])
        ]
      ])*/
    ;
  
  /*  $builder->get('shipping_city_name')->addEventListener(
      FormEvents::POST_SUBMIT,
      function (\Symfony\Component\Form\FormEvent $event) use ($formModifier) {
        $data = $event->getForm()->getData();
        $formModifier($event->getForm()->getParent(), $data);
      }
    );*/
    
/*    $builder->add('shippingMethod', null, [
      'required' => true,
      'expanded' => true
    ]);*/
    /*    if ($city === 'Реж' || $city === 'Екатеринбург')
        {*/
    /*      $builder
            ->add('shipping_method_id', EntityType::class, [
              'label' => 'Метод доставки',
              'class' => ShippingMethod::class,
             /* 'query_builder' => function (EntityRepository $er) use ($data)
              {
                return $er->createQueryBuilder('sm')
                  ->where('sm.uid != :uid')
                  ->orderBy('sm.position')
                  ->setParameter('uid', ShippingMethodUserDefined::UID);
              },
              'required' => true,
              'error_bubbling' => false,
              'constraints' => [
                new NotBlank(['message' => 'Пожалуйста, укажите способ оплаты заказа'])
              ],
              'expanded' => true*/
    /*
    'data' => function (EntityRepository $er) use ($data)
    {
      return $er->createQueryBuilder('sm')
        ->where('sm.uid = :uid')
        ->orderBy('sm.position')
        ->setParameter('uid', ShippingMethodStorePickup::UID)
        ->getQuery()->getOneOrNullResult();
    },
   # 'expanded' => true,
  ]);*/
    /*} else
    {
      $builder
        ->add('shipping_method_id', HiddenType::class, [
          'label' => 'Метод доставки',
          'class' => ShippingMethod::class,
          'required' => true,
          'data' => function (EntityRepository $er) use ($data)
          {
            return $er->createQueryBuilder('sm')
              ->where('sm.uid = :uid')
              ->orderBy('sm.position')
              ->setParameter('uid', ShippingMethodUserDefined::UID)
              ->getQuery()->getOneOrNullResult();
          },
          'expanded' => true,
          'error_bubbling' => false,
          'constraints' => [
            new NotBlank(['message' => 'Пожалуйста, укажите способ оплаты заказа'])
          ]
        ]);
    }*/
    
    /*$builder->get('shipping_method_id')->addEventListener(
      FormEvents::POST_SUBMIT,
      function (FormEvent $event) use ($data) {
        // It's important here to fetch $event->getForm()->getData(), as
        // $event->getData() will get you the client data (that is, the ID)
        $data = $event->getForm()->getData();
      
        // since we've added the listener to the child, we'll have to pass on
        // the parent to the callback functions!
        $event->getForm()->setData($data);
      }
    );*/
    
    
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