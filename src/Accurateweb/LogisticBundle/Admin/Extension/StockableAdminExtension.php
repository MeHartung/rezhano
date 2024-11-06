<?php

namespace Accurateweb\LogisticBundle\Admin\Extension;

use Accurateweb\LogisticBundle\Model\ProductStockInterface;
use Accurateweb\LogisticBundle\Model\StockableInterface;
use Accurateweb\LogisticBundle\Model\WarehouseRepositoryInterface;
use Accurateweb\LogisticBundle\Validator\Constraints\OneCityStock;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;

class StockableAdminExtension extends AbstractAdminExtension
{
  private $warehouseRepository;
  private $productStockClass;

  /**
   * StockableAdminExtension constructor.
   * @param WarehouseRepositoryInterface $warehouseRepository
   * @param $productStockClass
   */
  public function __construct (WarehouseRepositoryInterface $warehouseRepository, $productStockClass)
  {
    $this->warehouseRepository = $warehouseRepository;
    $this->productStockClass = $productStockClass;
  }

  public function configureFormFields (FormMapper $formMapper)
  {
    $subject = $formMapper->getAdmin()->getSubject();

    if (!$subject instanceof StockableInterface)
    {
      return;
    }

    if ($formMapper->hasOpenTab())
    {
      $formMapper->end();
    }

    $formMapper
      ->tab('Количество')
      ->add('stocks', 'Sonata\CoreBundle\Form\Type\CollectionType', [
        'btn_add' => false,
        'type_options' => [
          'delete' => false,
          'btn_add' => false,
        ],
        'by_reference' => false,
      ], [
        'edit' => 'inline',
        'inline' => 'table',
      ])
      ->end();
  }

  /**
   * @param AdminInterface $admin
   * @param $product StockableInterface
   */
  public function alterObject (AdminInterface $admin, $product)
  {
    if (!$product instanceof StockableInterface)
    {
      return;
    }

    $warehouses = $this->warehouseRepository->findActiveWarehouses();

    if (count($product->getStocks()) != count($warehouses))
    {
      $currentWarehouses = [];
      $stockEntity = $this->productStockClass;

      foreach ($product->getStocks() as $stock)
      {
        $currentWarehouses[$stock->getWarehouse()->getId()] = $stock;
      }

      foreach ($warehouses as $warehouse)
      {
        if (!isset($currentWarehouses[$warehouse->getId()]))
        {
          /** @var ProductStockInterface $stock */
          $stock = new $stockEntity();
          $stock->setValue(0);
          $stock->setWarehouse($warehouse);
          $stock->setProduct($product);
          $product->addStock($stock);
        }
      }
    }
  }

}