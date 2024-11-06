<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 07.02.18
 * Time: 16:28
 */

namespace StoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use StoreBundle\Entity\Setting;
use StoreBundle\Entity\Store\Order\Order;
use StoreBundle\Entity\Store\Order\OrderItem;
use StoreBundle\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveAbadonedCartCommand extends ContainerAwareCommand
{
  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setName('cart:remove-abandoned')
      ->setDescription("Remove carts where age > N days. Default: 30 days")
      ->addOption('age', null, InputOption::VALUE_OPTIONAL);
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @throws \Exception
   * @return int
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $container = $this->getContainer();
    /** @var EntityManagerInterface $em */
    $em = $container->get('doctrine.orm.entity_manager');
    $repository = $em->getRepository(Order::class);

    $age = is_null($input->getOption('age')) ?
      (int)$this->getContainer()->get('aw.settings.manager')
        ->getValue(Setting::SETTING_ABANDONED_CART_AGE) : (int)$input->getOption('age');

    $cartsForRemove = $repository->findAbandonedCarts((int)$age);

    $countCarts = count($cartsForRemove);
    $nbRemovedCarts = 0;

    $logger = $container->get('logger');

    if ($countCarts > 0)
    { /** @var $cart Order */
      foreach ($cartsForRemove as $cart)
      {
        try
        {
          // После добавления OrderTotalCalculateSubscriber исп. remove не представляется возможным, т.к.
          // вызов remove вызывает слушателся preUpdate, который не нужен.
          $em->createQuery("DELETE FROM StoreBundle:Store\Order\Order o WHERE o.id = :orderId")
             ->setParameter('orderId', $cart->getId())
             ->getResult();

          /*
           * Тоже костыль
           $events = $em->getClassMetadata(get_class($cart))->lifecycleCallbacks;
           $em->getClassMetadata(get_class($cart))->setLifecycleCallbacks(array());
           $em->remove($cart);
           $em->flush();
           $em->getClassMetadata(get_class($cart))->setLifecycleCallbacks($events);*/
        } catch (\Exception $exception)
        {
          $logger->addError($exception->getMessage());
          $output->writeln('Order with id' .$cart->getId(). ' not removed: ' . $exception->getMessage());

          continue;
        }

        $nbRemovedCarts++;
      }

      $logger->addInfo($countCarts . ' abandoned carts removed, where age > ' . (int)$age);

    }

    $output->writeln('Removed ' . $nbRemovedCarts . "/" . $countCarts . ' abandoned carts where age > ' . (int)$age . ' days');
    return 0;
  }
}