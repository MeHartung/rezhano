<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 08.02.18
 * Time: 11:17
 */

namespace StoreBundle\Unit\Command;


use Accurateweb\SettingBundle\Model\Manager\SettingManagerInterface;
use StoreBundle\Command\RemoveAbadonedCartCommand;
use StoreBundle\DataFixtures\Order\AbandonedCartCommandTestFixtures;
use StoreBundle\DataFixtures\Setting\SettingFixtures;
use StoreBundle\Entity\Setting;
use StoreBundle\Entity\Store\Order\Order;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\StoreBundle\StoreWebTestCase;

class OrderRemoveTest extends StoreWebTestCase
{
  /** @var  SettingManagerInterface */
  private $settings;

  public function setUp()
  {
    parent::setUp();
    $this->appendFixture(new SettingFixtures());
    $this->settings = $this->client->getContainer()->get('aw.settings.manager');
  }

  /**
   * Проверим, что на пустой табл. ничего не упадёт
   */
  public function testCommandIfEmptyOrdersTable()
  {
    $orders = $this->em->getRepository(Order::class)->findAll();

    if($orders)
    {
      foreach ($orders as $order)
      {
        $dql = sprintf("DELETE FROM StoreBundle:Store\Order\Order o WHERE o.id=%s", $order->getId());
        $this->em->createQuery($dql)->getResult();

      }
    }

    $this->em->clear();

    $orders = $this->em->getRepository(Order::class)->findAll();

    /**
     * Проверим, что табл. пуста
     */
    $this->assertEquals(0, count($orders));

    /**
     * Проверим, что на пустой табл. ничего не поломалось
     */
    $output = $this->runCommand();
    $this->assertEquals('Removed 0/0 abandoned carts where age > 30 days', trim($output));
  }

  public function testCommandIfNotEmptyOrdersTable()
  {
    $this->appendFixture(new AbandonedCartCommandTestFixtures(), true);

    # По данным из фикстур должна быть удалена 1 корзина
    $output = $this->runCommand();
    $this->assertEquals(sprintf('Removed 1/1 abandoned carts where age > %s days', $this->settings->getValue(Setting::SETTING_ABANDONED_CART_AGE)), trim($output));

    # Сделаем вторую корзину старше
    $this->appendFixture(new AbandonedCartCommandTestFixtures(), true);

    $cart = $this->getByReference('should-not-removed-order');
    $cart->setUpdatedAt(new \DateTime('1980-01-12'));
    $this->em->persist($cart);
    $this->em->flush();
    $this->em->clear();

    $output = $this->runCommand();
    # Обе корзины должны быть удалены
    $this->assertEquals(sprintf('Removed 2/2 abandoned carts where age > %s days', $this->settings->getValue(Setting::SETTING_ABANDONED_CART_AGE)), trim($output),'Корзина не была удалена, а надо было.');

    #Смотрим, что не осталось корзин вообще
    $carts = $this->em->getRepository(Order::class)->findAll();
    $this->assertSame(null, $carts, 'Не все корзины были удалены');

    $this->appendFixture(new AbandonedCartCommandTestFixtures(), true);

    # установим срок ~55 лет
    $setting = $this->em->getRepository(Setting::class)->findOneBy(['name'=>Setting::SETTING_ABANDONED_CART_AGE]);
    $setting->setAbandonedCartAge(20000);
    $this->em->persist($setting);
    $this->em->flush();

    $output = $this->runCommand();
    #Обе корзины не должны быть удалены, т.к. настрокой задано старше 20000 дней
    $this->assertEquals(sprintf('Removed 0/0 abandoned carts where age > %s days',
                        $this->settings->getValue(Setting::SETTING_ABANDONED_CART_AGE)), trim($output),
                "Была удалена лишняя корзина, либо кто-то добавил корзину старше 55 лет.");
  }


  public function runCommand()
  {
    $application = $this->application;

    $application->add(new RemoveAbadonedCartCommand());

    $command = $application->find('cart:remove-abandoned');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array(
      'command'  => $command->getName(),
    ));

    return $output = $commandTester->getDisplay();
  }
}