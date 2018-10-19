<?php
/**
 * (c) 2017 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 * Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 * (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 * Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 * ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 * как нарушение его авторских прав.
 *
 * Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

namespace StoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use StoreBundle\Entity\Store\Catalog\Product\Product;
use StoreBundle\Resolver\Product\ProductPublicationManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Управляет публикацией товаров на основе заданных правил публикации
 *
 * @package StoreBundle\EventListener
 */
class ProductPublicationSupervisor implements EventSubscriber
{
  private $productPublicationManager;

  public function __construct (ProductPublicationManager $productPublicationManager)
  {
    $this->productPublicationManager = $productPublicationManager;
  }

  /**
   * Returns an array of events this subscriber wants to listen to.
   *
   * @return array
   */
  public function getSubscribedEvents()
  {
    return array(
      'prePersist',
      'preUpdate'
    );
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $this->superviseProductPublication($args);
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $this->superviseProductPublication($args);
  }

  /**
   *
   * @param LifecycleEventArgs $args
   */
  public function superviseProductPublication(LifecycleEventArgs $args)
  {
    $object = $args->getObject();

    if ($object instanceof Product)
    {
      $object->setPublished($this->productPublicationManager->canPublish($object));
    }
  }
}