<?php

namespace Accurateweb\MoyskladIntegrationBundle\Command;

use Accurateweb\MoyskladIntegrationBundle\Exception\MoyskladException;
use AppBundle\Entity\Store\Integration\MoyskladQueue;
use MoySklad\Exceptions\RequestFailedException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoyskladOrderSendCommand extends ContainerAwareCommand
{
  protected function configure ()
  {
    $this
      ->setName('moysklad:order-send')
      ->setDescription('Send last orders to Moy Sklad');
  }

  protected function execute (InputInterface $input, OutputInterface $output)
  {
    $moysklad_sender = $this->getContainer()->get('moysklad.sender');
    $em = $this->getContainer()->get('doctrine.orm.entity_manager');
    $queue = $em->getRepository('AppBundle:Store\Integration\MoyskladQueue')->findNotSuccessfullySent();

    /** @var MoyskladQueue $item */
    foreach ($queue as $item)
    {
      $order = $item->getOrder();
      $item->setMessage(null);

      try
      {
        $moysklad_sender->sendOrder($order);
        $output->writeln(sprintf('Order %s sending...', $order->getDocumentNumber()));
        $output->writeln('ok');
      }
      /*catch (MoyskladUniqueFieldException $e)
      {
        //Если нарушение уникальности поля 'name', значит заказа с таким именем уже существует в базе
        if  (preg_match('/\'([a-z]+)\'/', $e->getMessage(), $m))
        {
          $field = $m[1];

          if ($field == 'name')
          {
            $order->setMoyskladSent(true);
            $em->persist($order);
          }
        }
      }*/
      catch (MoyskladException $e)
      {
        $item->setMessage(sprintf('%s.', $e->getMessage()));
        $output->writeln(sprintf('[%s]%s. %s', $e->getCode(), $e->getMessage(), $e->getInfo()));
        $this->getContainer()->get('logger')->error(sprintf('[%s]%s. %s', $e->getCode(), $e->getMessage(), $e->getInfo()));
      }
      catch (RequestFailedException $e)
      {
        $req = $e->getRequest();
        $resp = $e->getResponse();
        $this->getContainer()->get('logger')->error(sprintf('%s', json_encode($e->getDump())));

        try
        {
          $from = $this->getContainer()->getParameter('operator_email');
          $to = $this->getContainer()->getParameter('service_desc_email');
          $email = $this->getContainer()->get('aw_email_templating.template.factory')->createMessage(
            'moysklad_order_failed',
            array($from => 'Интернет-магазин Evolve'),
            array($to => 'ServiceDesk'),
            [
              'request' => json_encode($req),
              'response' => json_encode($resp),
            ]
          );

          $this->getContainer()->get('mailer')->send($email);
        }
        catch (\Swift_TransportException $e)
        {
          $this->getContainer()->get('logger')->error(sprintf('Unable to send email for ServiceDesc: "%s"', $e->getMessage()));
        }
      }

      $item->setSentAt(new \DateTime('now'));
      $em->persist($item);
    }

    $em->flush();
  }

}
