<?php

namespace Accurateweb\LogisticBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AccuratewebLogisticExtension extends Extension
{
  /**
   * {@inheritdoc}
   */
  public function load (array $configs, ContainerBuilder $container)
  {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yml');

    /*
     * Админка складов
     */
    $adminWarehouse = $container->getDefinition('aw.logistic.warehouse.admin');
    $adminWarehouse->setArgument(1, $config['entities']['warehouse']);

    /*
     * Админка городов
     */
    $adminCity = $container->getDefinition('aw.logistic.city.admin');
    $adminCity->setArgument(1, $config['entities']['city']);

    /*
     * Админка ПВЗ
     */
    $adminCity = $container->getDefinition('aw.logistic.pickup_point.admin');
    $adminCity->setArgument(1, $config['entities']['pickup_point']);

    /*
     * Админка количества товара на складе
     */
    $adminProductStock = $container->getDefinition('aw.logistic.product_stock.admin');
    $adminProductStock->setArgument(1, $config['entities']['product_stock']);

    /*
     * Репозиторий складов
     */
    $warehouseRepository = $container->getDefinition('aw.repository.warehouse');
    $warehouseRepository->setArgument(0, $config['entities']['warehouse']);

    /*
     * Расширение админки товаров
     */
    $adminExtensionStockable = $container->getDefinition('aw.stockable.extension');
    $adminExtensionStockable->setArgument(1, $config['entities']['product_stock']);
  }
}
