<?php

namespace  Tests\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use StoreBundle\Entity\Store\Payment\Method\PaymentMethod;

class PaymentMethodFixture extends Fixture
{
  public function load (ObjectManager $manager)
  {
    $cash = new PaymentMethod();
    $cash
      ->setName('Наличными')
      ->setDescription('Взимается комиссия в зависимости от способа доставки. Без комиссии при получении в магазине или курьерской доставке по Екатеринбургу. 3% при доставке ТК СДЭК. 6% при доставке Почтой России')
      ->setEnabled(true)
      ->setPosition(0)
      ->setAvailabilityDecisionManagerId('a7e6486a-9016-4fd6-8ebc-1d3870f11782')
      ->setFeeCalculatorId('1f8312fb-f48f-443a-b915-bec36b7cc072');

    $card = new PaymentMethod();
    $card
      ->setName('Банковской картой')
      ->setDescription('Принимаем к оплате VISA и MasterCard')
      ->setEnabled(true)
      ->setPosition(1)
      ->setAvailabilityDecisionManagerId('d2eb7c63-dd58-44a3-9520-8749d72e1e16')
      ->setFeeCalculatorId('56bbfffe-a97e-4ad5-ada5-7a3749fc8a15');

    $bill = new PaymentMethod();
    $bill
      ->setName('Выставить счет')
      ->setDescription('Для оплаты в любом банке или через интернет-банк')
      ->setEnabled(true)
      ->setPosition(2)
      ->setAvailabilityDecisionManagerId('d2eb7c63-dd58-44a3-9520-8749d72e1e16')
      ->setFeeCalculatorId('56bbfffe-a97e-4ad5-ada5-7a3749fc8a15');

    $credit = new PaymentMethod();
    $credit
      ->setName('Купить в кредит')
      ->setDescription(null)
      ->setEnabled(true)
      ->setPosition(3)
      ->setAvailabilityDecisionManagerId('d2eb7c63-dd58-44a3-9520-8749d72e1e16')
      ->setFeeCalculatorId('56bbfffe-a97e-4ad5-ada5-7a3749fc8a15');

    $creditAlfa = new PaymentMethod();
    $creditAlfa
      ->setName('Купить в кредит Альфа банк')
      ->setDescription(null)
      ->setEnabled(true)
      ->setPosition(3)
      ->setAvailabilityDecisionManagerId('d2eb7c63-dd58-44a3-9520-8749d72e1e16')
      ->setFeeCalculatorId('56bbfffe-a97e-4ad5-ada5-7a3749fc8a15')
      ->setType(PaymentMethod::ALFA_TYPE_GUID)
    ;

    $creditTinkoff = new PaymentMethod();
    $creditTinkoff
      ->setName('Купить в кредит Альфа банк')
      ->setDescription(null)
      ->setEnabled(true)
      ->setPosition(3)
      ->setAvailabilityDecisionManagerId('d2eb7c63-dd58-44a3-9520-8749d72e1e16')
      ->setFeeCalculatorId('56bbfffe-a97e-4ad5-ada5-7a3749fc8a15')
      ->setType(PaymentMethod::TINKOFF_TYPE_GUID)
    ;

    $manager->persist($cash);
    $manager->persist($card);
    $manager->persist($bill);
    $manager->persist($credit);
    $manager->persist($creditTinkoff);
    $manager->persist($creditAlfa);
    $manager->flush();

    $this->addReference('payment-cash', $cash);
    $this->addReference('payment-card', $card);
    $this->addReference('payment-bill', $bill);
    $this->addReference('payment-credit', $credit);
    $this->addReference('payment-alfa', $creditAlfa);
    $this->addReference('payment-tinkoff', $creditTinkoff);
  }
}